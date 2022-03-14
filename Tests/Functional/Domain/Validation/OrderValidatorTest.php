<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tests\Unit\Domain\Validation;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Validation\OrderValidator;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Validation\Error;

/**
 * TODO: Rewrite this test to a real functional test!
 *
 * @testdox The order validator
 * @covers JWeiland\Reserve\Domain\Validator\OrderValidator
 */
class OrderValidatorTest extends FunctionalTestCase
{
    use ProphecyTrait;

    /*
    * @var array
    */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/reserve'
    ];

    /**
     * @test
     */
    public function addsResultsFromSignalSlot()
    {
        $subject = new OrderValidator();

        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->dispatch(
            OrderValidator::class,
            'validateOrder',
            Argument::type('array')
        )->will(function (array $arguments) {
            $slotArguments = $arguments[2];

            $result = new Result();
            $result->addError(new Error('Example error', 101010));

            /** @var ObjectStorage $errors */
            $errors = $slotArguments['errorResults'];
            $errors->attach($result);
        });
        $this->inject($subject, 'dispatcher', $dispatcher->reveal());

        $period = $this->prophesize(Period::class);
        $period->isBookable()->willReturn(true);
        $order = $this->prophesize(Order::class);
        $order->getEmail()->willReturn('valid@example.com');
        $order->getBookedPeriod()->willReturn($period->reveal());

        $result = $subject->validate($order->reveal());

        self::assertTrue($result->hasErrors());
        self::assertCount(1, $result->getErrors());
        self::assertSame('Example error', $result->getErrors()[0]->getMessage());
    }

    /**
     * @test
     */
    public function doesNotDispatchIfInstanceIsNotAnOrder()
    {
        $subject = new OrderValidator();

        $dispatcher = $this->prophesize(Dispatcher::class);
        $this->inject($subject, 'dispatcher', $dispatcher->reveal());

        $subject->validate(null);

        $dispatcher->dispatch(
            OrderValidator::class,
            'validateOrder',
            Argument::type('array')
        )->shouldNotBeCalled();

        $dispatcher->checkProphecyMethodsPredictions();
    }
}
