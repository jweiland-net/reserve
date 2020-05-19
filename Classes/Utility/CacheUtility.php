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

namespace JWeiland\Reserve\Utility;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility class for caching methods
 */
class CacheUtility extends AbstractUtility
{
    const FACILITY_CACHE_IDENTIFIER = 'tx_reserve_facility_';

    public static function addFacilityToCurrentPageCacheTags(int $facilityUid)
    {
        static::getTypoScriptFrontendController()->addCacheTags([static::FACILITY_CACHE_IDENTIFIER . $facilityUid]);
    }

    public static function clearPageCachesForPagesWithCurrentFacility(int $facilityUid)
    {
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesByTag(static::FACILITY_CACHE_IDENTIFIER . $facilityUid);
    }
}
