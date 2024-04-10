<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$iconsRegistered = [
    'ext-reserve-wizard-icon' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/Extension.svg',
    ],
];

// register icons for model TCA tables
$modelIcons = ['facility', 'order', 'order_1', 'period', 'reservation', 'email'];
foreach ($modelIcons as $modelIcon) {
    $identifier = 'tx_reserve_domain_model_' . $modelIcon;
    $iconsRegistered[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/' . $identifier . '.svg',
    ];
}

return $iconsRegistered;
