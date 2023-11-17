<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Hooks;

use JWeiland\Reserve\DataHandler\AskForMailAfterPeriodDeletion;
use JWeiland\Reserve\DataHandler\AskForMailAfterPeriodUpdate;
use JWeiland\Reserve\DataHandler\FacilityClearCacheAfterUpdate;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandler
{
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler): void
    {
        GeneralUtility::makeInstance(AskForMailAfterPeriodUpdate::class)
            ->processDataHandlerResultAfterAllOperations($dataHandler);
        GeneralUtility::makeInstance(FacilityClearCacheAfterUpdate::class)
            ->processDataHandlerResultAfterAllOperations($dataHandler);
    }

    public function processCmdmap_deleteAction(
        string $table,
        int $id,
        array $recordToDelete,
        bool $recordWasDeleted,
        \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
    ): void {
        GeneralUtility::makeInstance(AskForMailAfterPeriodDeletion::class)
            ->processDataHandlerCmdDeleteAction($table, $id, $recordToDelete, $recordWasDeleted, $dataHandler);
    }

    public function processCmdmap_afterFinish(\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler): void
    {
        GeneralUtility::makeInstance(AskForMailAfterPeriodDeletion::class)
            ->processDataHandlerCmdResultAfterFinish($dataHandler);
    }
}
