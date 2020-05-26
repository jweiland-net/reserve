<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace JWeiland\Reserve\Domain\Model\v8;

/**
 * Order for TYPO3 v8 because of annotations
 * @internal
 */
class Order extends \JWeiland\Reserve\Domain\Model\Order
{
    /**
     * @var \JWeiland\Reserve\Domain\Model\Period
     */
    protected $bookedPeriod;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Reservation>
     * @cascade remove
     */
    protected $reservations;
}
