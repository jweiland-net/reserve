<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Functional\Domain\Validation;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Validation\OrderValidator;
use JWeiland\Reserve\Event\ValidateOrderEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \JWeiland\Reserve\Domain\Validation\OrderValidator
 */
class OrderValidatorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'jweiland/reserve',
    ];

    /**
     * @test
     */
    public function addsResultsFromEvent(): void
    {
        $subject = new OrderValidator();

        $orderMock = $this->createMock(Order::class);
        $orderMock
            ->expects(self::atLeastOnce())
            ->method('getEmail')
            ->willReturn('valid@example.com');

        $result = new Result();
        $result->addError(new Error('Example error', 101010));

        $errorResults = new ObjectStorage();
        $errorResults->attach($result);

        $event = new ValidateOrderEvent($orderMock, $errorResults);

        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $eventDispatcherMock
            ->expects(self::atLeastOnce())
            ->method('dispatch')
            ->willReturn($event);

        GeneralUtility::setSingletonInstance(EventDispatcher::class, $eventDispatcherMock);

        $result = $subject->validate($orderMock);

        self::assertTrue($result->hasErrors());
        self::assertCount(1, $result->getErrors());
        self::assertSame('Example error', $result->getErrors()[0]->getMessage());
    }

    /**
     * @test
     */
    public function doesNotDispatchIfInstanceIsNotAnOrder(): void
    {
        $subject = new OrderValidator();

        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $eventDispatcherMock
            ->expects(self::never())
            ->method('dispatch');

        GeneralUtility::setSingletonInstance(EventDispatcher::class, $eventDispatcherMock);

        $subject->validate(null);
    }
}
