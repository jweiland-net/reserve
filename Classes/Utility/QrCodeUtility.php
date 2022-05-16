<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\Result\ResultInterface;
use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Utility to generate QrCodes for a reservation.
 */
class QrCodeUtility
{
    public static function generateQrCode(Reservation $reservation): ResultInterface
    {
        $bookedPeriod = $reservation->getCustomerOrder()->getBookedPeriod();
        $begin = $bookedPeriod->getBegin() instanceof \DateTime ? $bookedPeriod->getBegin()->format('H:i') : '00:00';

        $builder = Builder::create()
            ->data($reservation->getCode())
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->labelText(sprintf(
                '%s %s %s %s',
                $bookedPeriod->getFacility()->getShortName() ?: $bookedPeriod->getFacility()->getName(),
                strftime(
                    LocalizationUtility::translate('date_format', 'reserve'),
                    $bookedPeriod->getDate()->getTimestamp()
                ),
                $begin,
                $bookedPeriod->getEnd() ? (' - ' . $bookedPeriod->getEnd()->format('H:i')) : ''
            ))
            ->labelAlignment(new LabelAlignmentCenter());

        static::applyQrCodeSettingsFromFacility($builder, $bookedPeriod->getFacility());

        return $builder->build();
    }

    protected static function applyQrCodeSettingsFromFacility(BuilderInterface $builder, Facility $facility): void
    {
        $builder
            ->labelFont(new NotoSans($facility->getQrCodeLabelSize()));

        if ($facility->getQrCodeLogo()->count()) {
            $builder
                ->logoPath(GeneralUtility::getFileAbsFileName(current($facility->getQrCodeLogo()->toArray())->getOriginalResource()->getPublicUrl()))
                ->logoResizeToWidth($facility->getQrCodeLogoWidth());
        }
    }
}
