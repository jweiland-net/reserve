<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use JWeiland\Reserve\Configuration\ExtConf;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Utility to get and set the taken orders from current session
 *
 * This may prevent primitive users from placing multiple orders.
 * This is not a security feature cause a clever guy just switches
 * the browser or clears the cookies to get more tickets and make other guys crying ;)
 */
class OrderSessionUtility
{
    private const SESSION_KEY = 'tx_reserve_orders';

    public static function blockNewOrdersForFacilityInCurrentSession(int $facilityUid, ServerRequestInterface $request): void
    {
        // [<facility_uid> => <timestamp_of_confirmation>]
        $orders = [];
        if (self::getFrontendUserAuthentication($request)->getSessionData(self::SESSION_KEY)) {
            $orders = self::getFrontendUserAuthentication($request)->getSessionData(self::SESSION_KEY);
        }

        $orders[$facilityUid] = time();
        self::getFrontendUserAuthentication($request)->setAndSaveSessionData(self::SESSION_KEY, $orders);
    }

    public static function unblockNewOrdersForFacilityInCurrentSession(int $facilityUid, ServerRequestInterface $request): void
    {
        // [<facility_uid> => <timestamp_of_confirmation>]
        $orders = [];
        if (self::getFrontendUserAuthentication($request)->getSessionData(self::SESSION_KEY)) {
            $orders = self::getFrontendUserAuthentication($request)->getSessionData(self::SESSION_KEY);
        }

        unset($orders[$facilityUid]);

        self::getFrontendUserAuthentication($request)->setAndSaveSessionData(self::SESSION_KEY, $orders);
    }

    public static function isUserAllowedToOrder(int $facilityUid, ServerRequestInterface $request): bool
    {
        $allowed = true;
        if (
            ($orders = self::getFrontendUserAuthentication($request)->getSessionData(self::SESSION_KEY))
            && array_key_exists($facilityUid, $orders)
        ) {
            $allowed = (time() - $orders[$facilityUid]) > self::getExtConf()->getBlockMultipleOrdersInSeconds();
        }

        return $allowed;
    }

    private static function getFrontendUserAuthentication(ServerRequestInterface $request): FrontendUserAuthentication
    {
        return $request->getAttribute('frontend.user');
    }

    private static function getExtConf(): ExtConf
    {
        return GeneralUtility::makeInstance(ExtConf::class);
    }
}
