<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\DataHandler;

use JWeiland\Reserve\Hook\PageRendererHook;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Service to process the result of the DataHandler hook processDatamap_afterAllOperations
 * to notify the user about changes of periods and ask them to send a mail to the visitors.
 */
class AskForMailAfterPeriodUpdate
{
    private const TABLE = 'tx_reserve_domain_model_period';

    protected DataHandler $dataHandler;

    protected array $updatedRecords = [];

    public function processDataHandlerResultAfterAllOperations(DataHandler $dataHandler): bool
    {
        if (!array_key_exists(self::TABLE, $dataHandler->datamap)) {
            return false;
        }

        $this->dataHandler = $dataHandler;
        if (!Environment::isCli()) {
            $this->checkForUpdatedRecords();
            if (($this->updatedRecords !== []) && $this->checkIfUpdatedRecordsAffectsOrders()) {
                $this->addJavaScriptAndSettingsToPageRenderer();
            }
        }
        return true;
    }

    protected function checkForUpdatedRecords(): void
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
            if (!str_contains($recordId, self::TABLE)) {
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

    protected function checkIfUpdatedRecordsAffectsOrders(): bool
    {
        $queryBuilder = $this->getQueryBuilderForTable('tx_reserve_domain_model_order');

        return (bool)$queryBuilder
            ->count('uid')
            ->from('tx_reserve_domain_model_order')
            ->where($queryBuilder->expr()->in('booked_period', implode(',', $this->updatedRecords)))
            ->executeQuery()
            ->fetchOne();
    }

    protected function addJavaScriptAndSettingsToPageRenderer(): void
    {
        // Get PID of first period and create email record on same pid
        $connection = $this->getConnectionForTable(self::TABLE);
        $row = $connection->select(
            ['pid'],
            self::TABLE,
            [
                'uid' => current($this->updatedRecords),
            ]
        )->fetchAssociative();

        $params = [
            'edit' => ['tx_reserve_domain_model_email' => [$row['pid'] => 'new']],
            'returnUrl' => '#txReserveCloseModal',
            'defVals' => [
                'tx_reserve_domain_model_email' => [
                    'body' => LocalizationUtility::translate('email.body.afterPeriodUpdate', 'reserve'),
                    'periods' => implode(',', $this->updatedRecords),
                ],
            ],
            'noView' => true,

        ];

        $uriBuilder = $this->getUriBuilder();

        // Add configuration to tx_reserve_modal in user session. This will be checked inside the PageRenderer hook
        // Class: JWeiland\Reserve\Hook\PageRenderer->processTxReserveModalUserSetting()
        $this->getBackendUserAuthentication()->setAndSaveSessionData(
            PageRendererHook::MODAL_SESSION_KEY,
            [
                'requireJsModules' => [
                    'TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule' => null,
                ],
                'inlineSettings' => [
                    'reserve.showModal' => [
                        'title' => LocalizationUtility::translate('modal.periodAskForMail.title', 'reserve'),
                        'message' => LocalizationUtility::translate('modal.periodAskForMail.message', 'reserve'),
                        'uri' => (string)$uriBuilder->buildUriFromRoute('record_edit', $params),
                    ],
                ],
                'inlineLanguageLabel' => [
                    'reserve.modal.button.writeMail' => LocalizationUtility::translate(
                        'modal.button.writeMail',
                        'reserve'
                    ),
                ],
            ]
        );
    }

    protected function getConnectionForTable(string $table): Connection
    {
        return $this->getConnectionPool()->getConnectionForTable($table);
    }

    protected function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder;
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }

    protected function getUriBuilder(): UriBuilder
    {
        return GeneralUtility::makeInstance(UriBuilder::class);
    }
}
