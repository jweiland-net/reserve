<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Reserve\Controller\Backend\QrCodePreviewController;

return [
    'tx_reserve_qr_code_preview' => [
        'path' => '/tx-reserve/qr-code/preview',
        'target' => QrCodePreviewController::class . '::ajaxAction',
    ],
];
