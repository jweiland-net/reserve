<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/**
 * With TYPO3 13 all plugins must be declared as content elements (CType) instead of "list_type"
 */
#[UpgradeWizard('reserve_migratePluginsToContentElementsUpdate')]
class PluginToContentElementUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'reserve_management' => 'reserve_management',
            'reserve_reservation' => 'reserve_reservation',
        ];
    }

    public function getTitle(): string
    {
        return '[reserve] Migrate plugins to Content Elements';
    }

    public function getDescription(): string
    {
        return 'The modern way to register plugins for TYPO3 is to register them as content element types. ' .
            'Running this wizard will migrate all reserve plugins to content element (CType)';
    }
}
