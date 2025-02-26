<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\ViewHelpers;

use JWeiland\Reserve\Service\ReserveService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * This ViewHelpers allows you to embed an information monitor with registration link for a period of a facility.
 *
 * Take a look into the documentation for more information.
 *
 * Example usage:
 * <jw:periodRegistration facilityUid="3" dateAndBegin="2051548200">
 *   <f:if condition="{periods}">
 *     <f:then>
 *       <f:comment><!--Most time there is just one period!--></f:comment>
 *       <f:for each="{periods}" as="period">
 *         <p>Remaining participants: {period.remainingParticipants}</p>
 *         <f:if condition="{period.isBookable}">
 *           <f:link.action pageUid="45" extensionName="reserve" pluginName="Reservation" controller="Checkout" action="form" arguments="{period: period}">Get a ticket!</f:link.action><br />
 *           <f:link.action pageUid="19" extensionName="reserve" pluginName="Reservation" controller="Checkout" action="list">Show all periods</f:link.action>
 *         </f:if>
 *       </f:for>
 *     </f:then>
 *     <f:else>
 *       Could not find any period for given time.
 *     </f:else>
 *   </f:if>
 * </jw:periodRegistration>
 */
class PeriodRegistrationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'facilityUid',
            'int',
            'The uid of the facility',
            true,
        );
        $this->registerArgument(
            'dateAndBegin',
            'int',
            'Timestamp of period "date" and "begin".',
            true,
        );
        $this->registerArgument(
            'as',
            'string',
            'Variable name that contains an array with Period objects',
            false,
            'periods',
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): string {
        $dateAndBegin = new \DateTime();
        $dateAndBegin->setTimestamp($arguments['dateAndBegin']);

        $renderingContext->getVariableProvider()->add(
            $arguments['as'],
            self::getReserveService()->findPeriodsByDateAndBegin($arguments['facilityUid'], $dateAndBegin),
        );
        $result = $renderChildrenClosure();
        $renderingContext->getVariableProvider()->remove($arguments['as']);

        return $result;
    }

    private static function getReserveService(): ReserveService
    {
        return GeneralUtility::makeInstance(ReserveService::class);
    }
}
