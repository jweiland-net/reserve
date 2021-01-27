<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Unit\Domain\Model;

use JWeiland\Reserve\Domain\Model\Period;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @testdox An period
 * @covers JWeiland\Reserve\Domain\Model\Period
 */
class PeriodTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider possibleRemainingCombinations
     */
    public function canReturnMaxFurtherParticipantsPerOrderAsIterableArray(
        int $maxParticipants,
        int $alreadyReserved,
        array $expectedResult
    ) {
        $subject = new Period();
        $subject->setMaxParticipants($maxParticipants);
        $subject->setMaxParticipantsPerOrder($maxParticipants);

        $this->inject($subject, 'cache', ['countReservations' => $alreadyReserved]);

        self::assertSame($expectedResult, $subject->getMaxFurtherParticipantsPerOrderIterable());
    }

    public function possibleRemainingCombinations(): array
    {
        return [
            'One remaining' => [
                'maxParticipants' => 2,
                'alreadyReserved' => 0,
                'expectedResult' => [1],
            ],
            'No remaining' => [
                'maxParticipants' => 2,
                'alreadyReserved' => 2,
                'expectedResult' => [],
            ],
        ];
    }
}
