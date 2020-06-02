<?php

declare(strict_types = 1);

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
