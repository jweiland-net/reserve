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
