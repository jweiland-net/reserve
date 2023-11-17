<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\DataHandler;

use JWeiland\Reserve\Domain\Model\Email;
use JWeiland\Reserve\Hooks\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AskForMailAfterPeriodDeletion implements SingletonInterface
{
    private const TABLE = 'tx_reserve_domain_model_period';

    protected array $visitorEmails = [];

    protected int $pid = 0;

    protected string $fromName = '';

    protected string $fromEmail = '';

    protected string $replyToName = '';

    protected string $replyToEmail = '';

    public function processDataHandlerCmdDeleteAction(
        string $table,
        int $id,
        array $recordToDelete,
        bool $recordWasDeleted,
        DataHandler $dataHandler
    ): void {
        if ($table !== self::TABLE || Environment::isCli()) {
            return;
        }

        $this->addVisitorEmailsOfPeriod($id);
    }

    public function addVisitorEmailsOfPeriod(int $periodUid): void
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::TABLE);

        $queryResult = $queryBuilder
            ->select('o.email', 'o.pid', 'f.from_name', 'f.from_email', 'f.reply_to_name', 'f.reply_to_email')
            ->from(self::TABLE, 'p')
            ->leftJoin('p', 'tx_reserve_domain_model_order', 'o', 'o.booked_period = p.uid')
            ->leftJoin('p', 'tx_reserve_domain_model_facility', 'f', 'f.uid = p.facility')
            ->where($queryBuilder->expr()->eq('p.uid', $queryBuilder->createNamedParameter($periodUid)))
            ->executeQuery();

        $firstPeriodRecord = [];
        $this->visitorEmails[$periodUid] = [];

        while ($periodRecord = $queryResult->fetchAssociative()) {
            // If no record could be found PID can be NULL, because of the leftJoin constraint
            if ($periodRecord['pid'] === null) {
                continue;
            }

            if ($firstPeriodRecord === []) {
                $firstPeriodRecord = $periodRecord;
            }

            $this->visitorEmails[$periodUid][] = $periodRecord['email'];
        }

        if ($firstPeriodRecord !== []) {
            $this->pid = (int)$firstPeriodRecord['pid'];
            $this->fromName = $firstPeriodRecord['from_name'];
            $this->fromEmail = $firstPeriodRecord['from_email'];
            $this->replyToName = $firstPeriodRecord['reply_to_name'];
            $this->replyToEmail = $firstPeriodRecord['reply_to_email'];
        }
    }

    public function processDataHandlerCmdResultAfterFinish(DataHandler $dataHandler): void
    {
        if ($this->visitorEmails !== [] && !Environment::isCli()) {
            foreach (array_keys($this->visitorEmails) as $periodUid) {
                if (!$dataHandler->hasDeletedRecord(self::TABLE, $periodUid)) {
                    // maybe the record was not removed but intended for removal and added to this array
                    unset($this->visitorEmails[$periodUid]);
                }
            }

            if ($this->visitorEmails !== []) {
                $this->addJavaScriptAndSettingsToPageRenderer();
            }
        }
    }

    protected function addJavaScriptAndSettingsToPageRenderer(): void
    {
        $params = [
            'edit' => ['tx_reserve_domain_model_email' => [$this->pid => 'new']],
            'returnUrl' => '#txReserveCloseModal',
            'defVals' => [
                'tx_reserve_domain_model_email' => [
                    'body' => LocalizationUtility::translate('email.body.afterPeriodDeletion', 'reserve'),
                    'receiver_type' => Email::RECEIVER_TYPE_MANUAL,
                    'from_name' => $this->fromName,
                    'from_email' => $this->fromEmail,
                    'reply_to_name' => $this->replyToName,
                    'reply_to_email' => $this->replyToEmail,
                    'custom_receivers' => implode(',', array_map(function ($a) {return implode(',', $a);}, $this->visitorEmails)),
                ],
            ],
            'noView' => true,
        ];

        $uriBuilder = $this->getUriBuilder();

        // Add configuration to tx_reserve_modal in user session. This will be checked inside the PageRenderer hook
        // Class: JWeiland\Reserve\Hooks\PageRenderer->processTxReserveModalUserSetting()
        $this->getBackendUserAuthentication()->setAndSaveSessionData(
            PageRenderer::MODAL_SESSION_KEY,
            [
                'jsInlineCode' => [
                    'Require-JS-Module-TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule' => 'require(["TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule"]);',
                ],
                'inlineSettings' => [
                    'reserve.showModal' => [
                        'title' => LocalizationUtility::translate(
                            'modal.periodAskForMailAfterDeletion.title',
                            'reserve'
                        ),
                        'message' => LocalizationUtility::translate(
                            'modal.periodAskForMailAfterDeletion.message',
                            'reserve'
                        ),
                        'uri' => (string)$uriBuilder->buildUriFromRoute('record_edit', $params),
                    ],
                ],
                'inlineLanguageLabel' => [
                    'reserve.modal.button.writeMail' => LocalizationUtility::translate('modal.button.writeMail', 'reserve'),
                ],
            ]
        );
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
