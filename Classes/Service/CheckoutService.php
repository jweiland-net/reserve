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
     * @param int $amountOfReservations
     * @param int $pid
     * @return bool true on success, otherwise false
     */
    public function checkout(Order $order, int $amountOfReservations, int $pid = 0): bool
    {
        $success = true;
        if (
            !$amountOfReservations
            || $amountOfReservations > $order->getBookedPeriod()->getMaxParticipantsPerOrder()
        ) {
            $success = false;
        } else {
            $order->setPid($pid);
            $order->setActivationCode(CheckoutUtility::generateActivationCodeForOrder());
            for ($i = 0; $i < $amountOfReservations; $i++) {
                /** @var Reservation $reservation */
                $reservation = GeneralUtility::makeInstance(Reservation::class);
                $reservation->setPid($pid);
                $reservation->setCustomerOrder($order);
                $reservation->setCode(CheckoutUtility::generateCodeForReservation());
                $order->getReservations()->attach($reservation);
                $this->persistenceManager->add($reservation);
            }
            $this->persistenceManager->add($order);
            $this->persistenceManager->persistAll();
            OrderSessionUtility::blockNewOrdersForFacilityInCurrentSession($order->getBookedPeriod()->getFacility()->getUid());
            CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());
        }
        return $success;
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

    public function sendReservationMail(Order $order)
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
            function (array $data, string $subject, string $bodyHtml, MailMessage $mailMessage, bool $isSymfonyEmail) {
                foreach ($data['order']->getReservations() as $reservation) {
                    $qrCode = QrCodeUtility::generateQrCode($reservation);
                    if ($isSymfonyEmail) {
                        $mailMessage->attach($qrCode->writeString(), $reservation->getCode(), $qrCode->getContentType());
                    } else {
                        $cid = $mailMessage->embed(\Swift_Image::newInstance($qrCode->writeString(), $reservation->getCode(), $qrCode->getContentType()));
                        $bodyHtml = str_replace('cid:' . $reservation->getCode(), $cid, $bodyHtml);
                    }
                }
                if (!$isSymfonyEmail) {
                    // apply modified cid's
                    $mailMessage->setBody($bodyHtml, 'text/html');
                }
            }
        );
    }
}
