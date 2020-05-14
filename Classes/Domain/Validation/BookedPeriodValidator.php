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

namespace JWeiland\Reserve\Domain\Validation;

use JWeiland\Reserve\Domain\Model\Period;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator to check if current $value is instance of Period and if it's bookable at the moment.
 */
class BookedPeriodValidator extends AbstractValidator
{
    protected function isValid($value)
    {
        if (!$value instanceof Period || ($value instanceof Period && !$value->isBookable())) {
            $this->addError('The selected period can not be booked at the moment!', 1589379319);
        }
    }
}
