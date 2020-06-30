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
 * Facility for TYPO3 v8 because of annotations
 * @internal
 */
class Facility extends \JWeiland\Reserve\Domain\Model\Facility
{
    /**
     * @lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Reserve\Domain\Model\Period>
     */
    protected $periods;

    /**
     * @lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $qrCodeLogo;
}
