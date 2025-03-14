<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Reservation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Service to generate QrCodes for a reservation.
 */
class QrCodeService
{
    public function generateQrCode(Reservation $reservation): ResultInterface
    {
        $bookedPeriod = $reservation->getCustomerOrder()->getBookedPeriod();
        $facility = $bookedPeriod->getFacility();

        return $this->buildQrCode(
            $reservation->getCode(),
            $this->generateLabelText($bookedPeriod),
            $facility->getQrCodeLabelSize(),
            $this->getLogoPath($facility),
            $facility->getQrCodeLogoWidth(),
        );
    }

    private function buildQrCode(
        string $data,
        string $labelText,
        int $labelFontSize,
        string $logoPath = '',
        int $logoWidth = 40,
    ): ResultInterface {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: $logoPath,
            logoResizeToWidth: $logoWidth,
            logoPunchoutBackground: true,
            labelText: $labelText,
            labelFont: new OpenSans($labelFontSize),
            labelAlignment: LabelAlignment::Center,
        );

        return $builder->build();
    }

    private function generateLabelText($bookedPeriod): string
    {
        $begin = $bookedPeriod->getBegin() instanceof \DateTime
            ? $bookedPeriod->getBegin()->format('H:i')
            : '00:00';

        return sprintf(
            '%s %s %s%s',
            $bookedPeriod->getFacility()->getShortName() ?: $bookedPeriod->getFacility()->getName(),
            $this->formatTime(LocalizationUtility::translate('date_format', 'reserve'), (int)$bookedPeriod->getDate()->getTimestamp()),
            $begin,
            $bookedPeriod->getEnd() ? (' - ' . $bookedPeriod->getEnd()->format('H:i')) : '',
        );
    }

    private function getLogoPath(Facility $facility): string
    {
        if ($facility->getQrCodeLogo()->count() > 0) {
            $firstQrCodeLogo = current($facility->getQrCodeLogo()->toArray());
            return GeneralUtility::getFileAbsFileName(
                $firstQrCodeLogo->getOriginalResource()->getPublicUrl(),
            );
        }

        return '';
    }

    private function formatTime(string $format, int $timestamp = null): string
    {
        $format = strtr($format, [
            '%a' => 'D', '%d' => 'd', '%m' => 'm', '%Y' => 'Y',
            '%H' => 'H', '%M' => 'i', '%S' => 's', '%B' => 'F',
        ]);

        return (new \DateTime())->setTimestamp($timestamp)->format($format);
    }
}
