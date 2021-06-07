<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Tca;

class CreateForeignTableColumns
{
    public function addEvents2DatabaseColumnsToTablesDefinition(array $sqlString): array
    {
        $sqlString[] = $this->getTableDefinitionForFacilityInEvents2Location();
        $sqlString[] = $this->getTableDefinitionForRegistrationRequiredInEvent();

        return [
            0 => $sqlString
        ];
    }

    /**
     * Add column (selector) to set reserve facility as relation to events2 location
     *
     * @return string
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
     *
     * @return string
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
