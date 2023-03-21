<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tca;

use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

class CreateForeignTableColumns
{
    public function addEvents2DatabaseColumnsToTablesDefinition(AlterTableDefinitionStatementsEvent $event): void
    {
        $event->addSqlData($this->getTableDefinitionForFacilityInEvents2Location());
        $event->addSqlData($this->getTableDefinitionForRegistrationRequiredInEvent());
    }

    /**
     * Add column (selector) to set reserve facility as relation to events2 location
     */
    protected function getTableDefinitionForFacilityInEvents2Location(): string
    {
        return sprintf(
            '%sCREATE TABLE %s (%s %s int(11) unsigned DEFAULT \'0\' NOT NULL%s);%s',
            str_repeat(PHP_EOL, 3),
            'tx_events2_domain_model_location',
            PHP_EOL,
            'facility',
            PHP_EOL,
            str_repeat(PHP_EOL, 3)
        );
    }

    /**
     * Add column (checkbox) to event table to define an event as registration required
     */
    protected function getTableDefinitionForRegistrationRequiredInEvent(): string
    {
        return sprintf(
            '%sCREATE TABLE %s (%s %s tinyint(1) unsigned DEFAULT \'0\' NOT NULL%s);%s',
            str_repeat(PHP_EOL, 3),
            'tx_events2_domain_model_event',
            PHP_EOL,
            'registration_required',
            PHP_EOL,
            str_repeat(PHP_EOL, 3)
        );
    }
}
