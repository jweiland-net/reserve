<?php

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-reserve-wizard-icon' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/Extension.svg',
    ],
    'tx_reserve_domain_model_facility' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_facility.svg',
    ],
    'tx_reserve_domain_model_order' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_order.svg',
    ],
    'tx_reserve_domain_model_order_1' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_order_1.svg',
    ],
    'tx_reserve_domain_model_period' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_period.svg',
    ],
    'tx_reserve_domain_model_reservation' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_reservation.svg',
    ],
    'tx_reserve_domain_model_email' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:reserve/Resources/Public/Icons/tx_reserve_domain_model_email.svg',
    ],
];
