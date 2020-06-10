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

namespace JWeiland\Reserve\Hooks;

use JWeiland\Reserve\DataHandler\AskForMailAfterPeriodDeletion;
use JWeiland\Reserve\DataHandler\AskForMailAfterPeriodUpdate;
use JWeiland\Reserve\DataHandler\FacilityClearCacheAfterUpdate;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandler
{
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler)
    {
        GeneralUtility::makeInstance(AskForMailAfterPeriodUpdate::class)->processDataHandlerResultAfterAllOperations($dataHandler);
        GeneralUtility::makeInstance(FacilityClearCacheAfterUpdate::class)->processDataHandlerResultAfterAllOperations($dataHandler);
    }

    public function processCmdmap_deleteAction(string $table, int $id, array $recordToDelete, bool $recordWasDeleted, \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler)
    {
        GeneralUtility::makeInstance(AskForMailAfterPeriodDeletion::class)->processDataHandlerCmdDeleteAction($table, $id, $recordToDelete, $recordWasDeleted, $dataHandler);
    }

    public function processCmdmap_afterFinish(\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler)
    {
        GeneralUtility::makeInstance(AskForMailAfterPeriodDeletion::class)->processDataHandlerCmdResultAfterFinish($dataHandler);
    }
}
