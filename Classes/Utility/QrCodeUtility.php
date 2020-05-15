<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace JWeiland\Reserve\Utility;

use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// include QR code library
@include 'phar://' . ExtensionManagementUtility::extPath('reserve') . 'Libraries/Dependencies.phar/vendor/autoload.php';

/**
 * Utility to generate QrCodes for a reservation.
 */
class QrCodeUtility
{
    public static function generateQrCode(Reservation $reservation): QrCode
    {
        /** @var QrCode $qrCode */
        $qrCode = GeneralUtility::makeInstance(QrCode::class, $reservation->getCode());
        $qrCode->setSize(350);

        $qrCode
            // todo: add some flexibility! Make at least the date format configurable
            ->setLabel(sprintf(
                '%s %s %s - %s',
                $reservation->getCustomerOrder()->getBookedPeriod()->getFacility()->getName(),
                $reservation->getCustomerOrder()->getBookedPeriod()->getDate()->format($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy']),
                $reservation->getCustomerOrder()->getBookedPeriod()->getBegin()->format('H:i'),
                $reservation->getCustomerOrder()->getBookedPeriod()->getEnd()->format('H:i')
            ),
                16,
                ExtensionManagementUtility::extPath('reserve') . 'Resources/Private/Fonts/noto_sans.otf',
                LabelAlignment::CENTER
            );

        return $qrCode;
    }
}
