<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Abstract utility class to be used by other Utility classes of ext:reserve
 */
abstract class AbstractUtility
{
    protected static function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
