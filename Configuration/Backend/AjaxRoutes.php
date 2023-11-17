<?php

declare(strict_types=1);

use JWeiland\Reserve\Controller\Backend\QrCodePreviewController;

return [
    'tx_reserve_qr_code_preview' => [
        'path' => '/tx-reserve/qr-code/preview',
        'target' => QrCodePreviewController::class . '::ajaxAction',
    ],
];
