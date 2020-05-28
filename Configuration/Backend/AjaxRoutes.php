<?php
return [
    'tx_reserve_qr_code_preview' => [
        'path' => '/tx-reserve/qr-code/preview',
        'target' => \JWeiland\Reserve\Controller\Backend\QrCodePreviewController::class . '::ajaxAction',
    ],
];
