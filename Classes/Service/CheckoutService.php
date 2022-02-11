<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Participant;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\CheckoutUtility;
use JWeiland\Reserve\Utility\FluidUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use JWeiland\Reserve\Utility\QrCodeUtility;
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
     * @var ConfigurationManager
     */
    protected $configurationManager;

    public function __construct(PersistenceManager $persistenceManager, ConfigurationManager $configurationManager)
    {
        $this->persistenceManager = $persistenceManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * Create $amountOfReservations reservation records and add them to $order.
     * Set activation code for $order and all new reservations inside the current $order.
     * Use $order after $checkout to proceed
     *
     * @param Order $order
     * @param int $pid
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
            /** @var Reservation $reservation */
            $reservation = GeneralUtility::makeInstance(Reservation::class);
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
            OrderSessionUtility::blockNewOrdersForFacilityInCurrentSession($order->getBookedPeriod()->getFacility()->getUid());
        }
        CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());
        return true;
    }

    /**
     * @param Order $order
     * @param int $furtherParticipants
     */
    protected function addFurtherParticipantsToOrder(Order $order, int $furtherParticipants): void
    {
        // the reservation owner
        $furtherParticipant = GeneralUtility::makeInstance(Participant::class);
        $furtherParticipant->setLastName($order->getLastName());
        $furtherParticipant->setFirstName($order->getFirstName());
        $order->getParticipants()->attach($furtherParticipant);

        // Add further participants if flexform setting "showFieldsForFurtherParticipants" is false (off) and
        // a number field was used for further participants
        for ($i = 0; $i < $furtherParticipants; $i++) {
            $furtherParticipant = GeneralUtility::makeInstance(Participant::class);
            $furtherParticipant->setFirstName(LocalizationUtility::translate('order.furtherParticipant', 'reserve') . ' ' . ($i + 1));
            $order->getParticipants()->attach($furtherParticipant);
        }
    }

    public function sendConfirmationMail(Order $order): bool
    {
        return GeneralUtility::makeInstance(MailService::class)->sendMailToCustomer(
            $order,
            $order->getBookedPeriod()->getFacility()->getConfirmationMailSubject(),
            FluidUtility::replaceMarkerByRenderedTemplate(
                '###ORDER_DETAILS###',
                'Confirmation',
                $order->getBookedPeriod()->getFacility()->getConfirmationMailHtml(),
                ['pageUid' => $GLOBALS['TSFE']->id, 'order' => $order]
            )
        );
    }

    public function confirm(Order $order): bool
    {
        $success = true;
        $order->setActivated(true);
        $this->sendReservationMail($order);
        $this->persistenceManager->add($order);
        $this->persistenceManager->persistAll();
        return $success;
    }

    public function sendReservationMail(Order $order): bool
    {
        return GeneralUtility::makeInstance(MailService::class)->sendMailToCustomer(
            $order,
            $order->getBookedPeriod()->getFacility()->getReservationMailSubject(),
            FluidUtility::replaceMarkerByRenderedTemplate(
                '###RESERVATION###',
                'Reservation',
                $order->getBookedPeriod()->getFacility()->getReservationMailHtml(),
                ['pageUid' => $GLOBALS['TSFE']->id, 'order' => $order]
            ),
            function (array $data, string $subject, string $bodyHtml, MailMessage $mailMessage) {
                foreach ($data['order']->getReservations() as $reservation) {
                    $qrCode = QrCodeUtility::generateQrCode($reservation);
                    $mailMessage->attach($qrCode->writeString(), $reservation->getCode(), $qrCode->getContentType());
                }
            }
        );
    }
}
