<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Helper methods to be used e.g. in CheckoutService and DataHandler hooks
 */
class CheckoutUtility
{
    public static function generateActivationCodeForOrder(): string
    {
        /** @var Random $random */
        $random = GeneralUtility::makeInstance(Random::class);
        return $random->generateRandomHexString(16);
    }

    public static function generateCodeForReservation(): string
    {
        return GeneralUtility::stdAuthCode(StringUtility::getUniqueId(), '', 9);
    }
}
