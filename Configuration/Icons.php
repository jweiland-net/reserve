<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$iconsRegistered = [
    'ext-reserve-wizard-icon' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/Extension.svg',
    ],
];

$modelIcons = ['facility', 'order', 'order_1', 'period', 'reservation', 'email'];
foreach ($modelIcons as $modelIcon) {
    $identifier = 'tx_reserve_domain_model_' . $modelIcon;
    $iconsRegistered[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/' . $identifier . '.svg',
    ];
}

return $iconsRegistered;
