<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ViewHelper to check if haystack contains in needle list.
 */
class IfStringInCommaSeparatedListViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('haystack', 'string', 'The comma separated list to search in', true);
        $this->registerArgument('needle', 'string', 'The string that you are looking for', true);
    }

    /**
     * @param array|null $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null): bool
    {
        return in_array($arguments['needle'], explode(',', (string)$arguments['haystack']), true);
    }
}
