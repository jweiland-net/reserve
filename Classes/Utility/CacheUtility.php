<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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

    public static function addFacilityToCurrentPageCacheTags(int $facilityUid): void
    {
        static::getTypoScriptFrontendController()->addCacheTags([static::FACILITY_CACHE_IDENTIFIER . $facilityUid]);
    }

    public static function clearPageCachesForPagesWithCurrentFacility(int $facilityUid): void
    {
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesByTag(static::FACILITY_CACHE_IDENTIFIER . $facilityUid);
    }
}
