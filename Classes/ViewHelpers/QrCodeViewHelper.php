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
use JWeiland\Reserve\Utility\QrCodeUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to render QrCodes inside an image tag
 * Example: <img src="{jw:qrCode(reservation: reservation)}" alt="{reservation.code}" title="QR Code" />
 */
class QrCodeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'reservation',
            Reservation::class,
            'Reservation instance',
            true
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        return QrCodeUtility::generateQrCode($arguments['reservation'])->getDataUri();
    }
}
