<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility\Traits;

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Trait to add FrontendUserAuthentication into your classes
 */
trait FrontendUserTrait
{
    protected static function getFrontendUserAuthentication(): FrontendUserAuthentication
    {
        return $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.user');
    }
}
