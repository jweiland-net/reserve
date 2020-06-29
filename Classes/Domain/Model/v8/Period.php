<?php

declare(strict_types = 1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Model\v8;

/**
 * Period for TYPO3 v8 because of annotations
 * @internal
 */
class Period extends \JWeiland\Reserve\Domain\Model\Period
{
    /**
     * @lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Order>
     */
    protected $orders;
}
