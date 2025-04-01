<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Configuration\ExtConf;
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Participant;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Event\SendReservationEmailEvent;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\CheckoutUtility;
use JWeiland\Reserve\Utility\FluidUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use JWeiland\Reserve\Utility\QrCodeUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CheckoutService
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var ExtConf
     */
    protected $extensionConfiguration;

    public function __construct(
        PersistenceManager $persistenceManager,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationManager $configurationManager,
        MailService $mailService,
        ExtConf $extensionConfiguration
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationManager = $configurationManager;
        $this->mailService = $mailService;
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * Create $amountOfReservations reservation records and add them to $order.
     * Set activation code for $order and all new reservations inside the current $order.
     * Use $order after $checkout to proceed
     *
     * @param int $furtherParticipants anonymized further participants (Name: Further participant <n>)
     * @return bool true on success, otherwise false
     */
    public function checkout(Order $order, int $pid = 0, int $furtherParticipants = 0): bool
    {
        $this->addFurtherParticipantsToOrder($order, $furtherParticipants);
        if ($order->canBeBooked() === false) {
            return false;
        }

        $order->setPid($pid);
        $order->setActivationCode(CheckoutUtility::generateActivationCodeForOrder());

        foreach ($order->getParticipants() as $participant) {
            $reservation = $this->getEmptyReservation();
            $reservation->setPid($pid);
            $reservation->setCustomerOrder($order);
            $reservation->setLastName($participant->getLastName());
            $reservation->setFirstName($participant->getFirstName());
            $reservation->setCode(CheckoutUtility::generateCodeForReservation());

            $order->getReservations()->attach($reservation);

            $this->persistenceManager->add($reservation);
        }

        $this->persistenceManager->add($order);
        $this->persistenceManager->persistAll();

        if ($order->shouldBlockFurtherOrdersForFacility()) {
            OrderSessionUtility::blockNewOrdersForFacilityInCurrentSession(
                $order->getBookedPeriod()->getFacility()->getUid()
            );
        }

        CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());

        return true;
    }

    protected function addFurtherParticipantsToOrder(Order $order, int $furtherParticipants): void
    {
        // The reservation owner
        $furtherParticipant = $this->getEmptyParticipant();
        $furtherParticipant->setLastName($order->getLastName());
        $furtherParticipant->setFirstName($order->getFirstName());

        $order->getParticipants()->attach($furtherParticipant);

        // Add further participants if flexform setting "showFieldsForFurtherParticipants" is false (off) and
        // a number field was used for further participants
        for ($i = 0; $i < $furtherParticipants; $i++) {
            $furtherParticipant = $this->getEmptyParticipant();
            $furtherParticipant->setFirstName(
                LocalizationUtility::translate('order.furtherParticipant', 'reserve') . ' ' . ($i + 1)
            );

            $order->getParticipants()->attach($furtherParticipant);
        }
    }

    public function sendConfirmationMail(Order $order): bool
    {
        return $this->mailService->sendMailToCustomer(
            $order,
            $order->getBookedPeriod()->getFacility()->getConfirmationMailSubject(),
            FluidUtility::replaceMarkerByRenderedTemplate(
                '###ORDER_DETAILS###',
                'Confirmation',
                $order->getBookedPeriod()->getFacility()->getConfirmationMailHtml(),
                [
                    'pageUid' => $GLOBALS['TSFE']->id,
                    'order' => $order,
                ]
            )
        );
    }

    public function confirm(Order $order): void
    {
        $order->setActivated(true);
        $this->sendReservationMail($order);
        $this->persistenceManager->add($order);
        $this->persistenceManager->persistAll();
    }

    public function sendReservationMail(Order $order): bool
    {
        return $this->mailService->sendMailToCustomer(
            $order,
            $order->getBookedPeriod()->getFacility()->getReservationMailSubject(),
            FluidUtility::replaceMarkerByRenderedTemplate(
                '###RESERVATION###',
                'Reservation',
                $order->getBookedPeriod()->getFacility()->getReservationMailHtml(),
                [
                    'pageUid' => $GLOBALS['TSFE']->id,
                    'order' => $order,
                    'configurations' => $this->extensionConfiguration,
                ]
            ),
            function (array $data, string $subject, string $bodyHtml, MailMessage $mailMessage) {
                foreach ($data['order']->getReservations() as $reservation) {
                    if (!$this->extensionConfiguration->getDisableQRCodeGeneration()) {
                        $qrCode = QrCodeUtility::generateQrCode($reservation);
                        $mailMessage->attach($qrCode->getString(), $reservation->getCode(), $qrCode->getMimeType());
                    }
                    /** @var SendReservationEmailEvent $event */
                    $event = $this->eventDispatcher->dispatch(
                        new SendReservationEmailEvent($mailMessage),
                    );
                    $mailMessage = $event->getMailMessage();
                }
            }
        );
    }

    private function getEmptyParticipant(): Participant
    {
        return GeneralUtility::makeInstance(Participant::class);
    }

    private function getEmptyReservation(): Reservation
    {
        return GeneralUtility::makeInstance(Reservation::class);
    }
}
