<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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

        $qrCode
            ->setLabel(
                sprintf(
                '%s %s %s - %s',
                $reservation->getCustomerOrder()->getBookedPeriod()->getFacility()->getShortName()
                ?: $reservation->getCustomerOrder()->getBookedPeriod()->getFacility()->getName(),
                strftime(
                    LocalizationUtility::translate('date_format', 'reserve'),
                    $reservation->getCustomerOrder()->getBookedPeriod()->getDate()->getTimestamp()
                ),
                $reservation->getCustomerOrder()->getBookedPeriod()->getBegin()->format('H:i'),
                $reservation->getCustomerOrder()->getBookedPeriod()->getEnd()->format('H:i')
            ),
                16,
                ExtensionManagementUtility::extPath('reserve') . 'Resources/Private/Fonts/noto_sans.otf',
                LabelAlignment::CENTER
            );

        static::applyQrCodeSettingsFromFacility($qrCode, $reservation->getCustomerOrder()->getBookedPeriod()->getFacility());

        return $qrCode;
    }

    protected static function applyQrCodeSettingsFromFacility(QrCode $qrCode, Facility $facility)
    {
        $qrCode
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setEncoding('UTF-8')
            ->setSize($facility->getQrCodeSize())
            ->setLabelFontSize($facility->getQrCodeLabelSize());
        if ($facility->getQrCodeLogo()->count()) {
            $qrCode->setLogoPath(GeneralUtility::getFileAbsFileName(current($facility->getQrCodeLogo()->toArray())->getOriginalResource()->getPublicUrl()));
            $qrCode->setLogoWidth($facility->getQrCodeLogoWidth());
        }
    }
}
