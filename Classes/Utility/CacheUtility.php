<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility class for caching methods
 */
class CacheUtility
{
    private const FACILITY_CACHE_IDENTIFIER = 'tx_reserve_facility_';

    public static function addFacilityToCurrentPageCacheTags(int $facilityUid, ServerRequestInterface $request): void
    {
        static::getCacheCollector($request)->addCacheTags([static::FACILITY_CACHE_IDENTIFIER . $facilityUid]);
    }

    public static function clearPageCachesForPagesWithCurrentFacility(int $facilityUid): void
    {
        self::getCacheManager()->flushCachesByTag(static::FACILITY_CACHE_IDENTIFIER . $facilityUid);
    }

    protected static function getCacheCollector(ServerRequestInterface $request)
    {
        return $request->getAttribute('frontend.cache.collector');
    }

    private static function getCacheManager(): CacheManager
    {
        return GeneralUtility::makeInstance(CacheManager::class);
    }
}
