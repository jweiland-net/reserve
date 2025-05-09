<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\ViewHelpers;

use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Service\QrCodeService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render QrCodes inside an image tag
 * Example: <img src="{jw:qrCode(reservation: reservation)}" alt="{reservation.code}" title="QR Code" />
 */
class QrCodeViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(protected readonly QrCodeService $qrCodeService) {}

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'reservation',
            Reservation::class,
            'Reservation instance',
            true,
        );
    }

    public function render(): string
    {
        return $this->qrCodeService
            ->generateQrCode($this->arguments['reservation'])
            ->getDataUri();
    }
}
