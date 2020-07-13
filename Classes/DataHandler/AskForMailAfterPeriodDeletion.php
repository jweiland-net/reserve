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
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Hooks\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AskForMailAfterPeriodDeletion implements SingletonInterface
{
    const TABLE = 'tx_reserve_domain_model_period';

    /**
     * @var array
     */
    protected $visitorEmails = [];

    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * @var string
     */
    protected $fromName = '';

    /**
     * @var string
     */
    protected $fromEmail = '';

    /**
     * @var string
     */
    protected $replyToName = '';

    /**
     * @var string
     */
    protected $replyToEmail = '';

    public function processDataHandlerCmdDeleteAction(string $table, int $id, array $recordToDelete, bool $recordWasDeleted, DataHandler $dataHandler)
    {
        if ($table !== self::TABLE) {
            return;
        }
        $this->addVisitorEmailsOfPeriod($id);
    }

    public function addVisitorEmailsOfPeriod(int $periodUid)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);
        $rows = $queryBuilder
            ->select('o.email', 'o.pid', 'f.from_name', 'f.from_email', 'f.reply_to_name', 'f.reply_to_email')
            ->from(self::TABLE, 'p')
            ->leftJoin('p', 'tx_reserve_domain_model_order', 'o', 'o.booked_period = p.uid')
            ->leftJoin('p', 'tx_reserve_domain_model_facility', 'f', 'f.uid = p.facility')
            ->where(
                $queryBuilder->expr()->eq('p.uid', $queryBuilder->createNamedParameter($periodUid)),
                $queryBuilder->expr()->neq('o.order_type', $queryBuilder->createNamedParameter(Order::TYPE_ARCHIVED))
            )
            ->execute()
            ->fetchAll();
        $this->visitorEmails[$periodUid] = [];
        foreach ($rows as $row) {
            $this->visitorEmails[$periodUid][] = $row['email'];
        }
        if (!empty($rows)) {
            $this->pid = $rows[0]['pid'];
            // this won't work if one deletes multiple periods of multiple facilities using the multi-selection mode
            $this->fromName = $rows[0]['from_name'];
            $this->fromEmail = $rows[0]['from_email'];
            $this->replyToName = $rows[0]['reply_to_name'];
            $this->replyToEmail = $rows[0]['reply_to_email'];
        }
    }

    public function processDataHandlerCmdResultAfterFinish(DataHandler $dataHandler)
    {
        if (!empty($this->visitorEmails)) {
            foreach (array_keys($this->visitorEmails) as $periodUid) {
                if (!$dataHandler->hasDeletedRecord(self::TABLE, $periodUid)) {
                    // maybe the record was not removed but intended for removal and added to this array
                    unset($this->visitorEmails[$periodUid]);
                }
            }
            if (!empty($this->visitorEmails)) {
                $this->addJavaScriptAndSettingsToPageRenderer();
            }
        }
    }

    protected function addJavaScriptAndSettingsToPageRenderer()
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
                        'title' => LocalizationUtility::translate('modal.periodAskForMailAfterDeletion.title', 'reserve'),
                        'message' => LocalizationUtility::translate('modal.periodAskForMailAfterDeletion.message', 'reserve'),
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
