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

namespace JWeiland\Reserve\DataHandler;

use JWeiland\Reserve\Hooks\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Service to process the result of the DataHandler hook processDatamap_afterAllOperations
 * to notify the user about changes of periods and ask them to send a mail to the visitors.
 */
class AskForMailAfterPeriodUpdate
{
    const TABLE = 'tx_reserve_domain_model_period';

    /**
     * @var DataHandler
     */
    protected $dataHandler;

    /**
     * @var array
     */
    protected $updatedRecords = [];

    public function processDataHandlerResultAfterAllOperations(DataHandler $dataHandler): bool
    {
        if (!array_key_exists(self::TABLE, $dataHandler->datamap)) {
            return false;
        }

        $this->dataHandler = $dataHandler;
        $this->checkForUpdatedRecords();
        if (!empty($this->updatedRecords)) {
            $this->addJavaScriptAndSettingsToPageRenderer();
        }

        return true;
    }

    protected function checkForUpdatedRecords()
    {
        if (!array_key_exists(self::TABLE, $this->dataHandler->datamap)) {
            return;
        }
        // This looks very dangerous but it's safe because we just use this to read
        // the historyRecords after all operations. In TYPO3 v10 $dataHandler->getHistoryRecords()
        // has been added, so replace this reflection when this extension requires TYPO3 >= v10
        $dataHandlerReflection = new \ReflectionClass($this->dataHandler);
        $historyRecordsProperty = $dataHandlerReflection->getProperty('historyRecords');
        $historyRecordsProperty->setAccessible(true);

        $checkFields = ['begin', 'end', 'date'];
        $updatedRecords = [];
        foreach ($historyRecordsProperty->getValue($this->dataHandler) as $recordId => $historyRecord) {
            if (strpos($recordId, self::TABLE) === false) {
                continue;
            }
            foreach ($checkFields as $checkField) {
                if (
                    array_key_exists($checkField, $historyRecord['oldRecord'])
                    && $historyRecord['oldRecord'][$checkField] !== $historyRecord['newRecord'][$checkField]
                ) {
                    // value has been updated
                    $updatedRecords[] = (int)str_replace(self::TABLE . ':', '', $recordId);
                }
            }
        }
        $this->updatedRecords = $updatedRecords;
    }

    protected function addJavaScriptAndSettingsToPageRenderer()
    {
        // get pid of first period and create email record on same pid
        /** @var \TYPO3\CMS\Core\Database\Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE);
        $row = $connection->select(['pid'], self::TABLE, ['uid' => current($this->updatedRecords)])->fetch();

        $params = [
            'edit' => ['tx_reserve_domain_model_email' => [$row['pid'] => 'new']],
            'returnUrl' => '#txReserveCloseModal',
            'defVals' => [
                'tx_reserve_domain_model_email' => [
                    'body' => LocalizationUtility::translate('email.body.afterPeriodUpdate', 'reserve'),
                    'periods' => implode(',', $this->updatedRecords)
                ]
            ],
            'noView' => true,

        ];
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        // Add configuration to tx_reserve_modal in user session. This will be checked inside the PageRenderer hook
        // Class: JWeiland\Reserve\Hooks\PageRenderer->processTxReserveModalUserSetting()
        $this->getBackendUserAuthentication()->setAndSaveSessionData(
            PageRenderer::MODAL_SESSION_KEY,
            [
                'jsInlineCode' => [
                    'Require-JS-Module-TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule' => 'require(["TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule"]);'
                ],
                'inlineSettings' => [
                    'reserve.showModal' => [
                        'title' => LocalizationUtility::translate('modal.periodAskForMail.title', 'reserve'),
                        'message' => LocalizationUtility::translate('modal.periodAskForMail.message', 'reserve'),
                        'uri' => (string)$uriBuilder->buildUriFromRoute('record_edit', $params)
                    ]
                ],
                'inlineLanguageLabel' => [
                    'reserve.modal.button.writeMail' => LocalizationUtility::translate('modal.button.writeMail', 'reserve')
                ]
            ]
        );
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
