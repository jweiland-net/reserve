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

use Doctrine\DBAL\Connection;
use JWeiland\Reserve\Utility\CacheUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Service to process the result of the DataHandler hook processDatamap_afterAllOperations
 * to check if caches of frontend facility list view needs to be cleared
 */
class FacilityClearCacheAfterUpdate
{
    /**
     * @var DataHandler
     */
    protected $dataHandler;

    public function processDataHandlerResultAfterAllOperations(DataHandler $dataHandler): bool
    {
        $this->dataHandler = $dataHandler;
        $facilityNames = [];
        // maybe split this big condition block into separated methods
        if (array_key_exists('tx_reserve_domain_model_facility', $dataHandler->datamap)) {
            foreach ($dataHandler->datamap['tx_reserve_domain_model_facility'] as $uid => $row) {
                CacheUtility::clearPageCachesForPagesWithCurrentFacility($uid);
                $facilityNames[] = $row['name'];
            }
        } elseif (array_key_exists('tx_reserve_domain_model_order', $dataHandler->datamap)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_reserve_domain_model_order');
            $queryBuilder
                ->select('f.uid', 'f.name')
                ->from('tx_reserve_domain_model_order', 'o')
                ->leftJoin('o', 'tx_reserve_domain_model_period', 'p', 'o.booked_period = p.uid')
                ->leftJoin('p', 'tx_reserve_domain_model_facility', 'f', 'p.facility = f.uid')
                ->where($queryBuilder->expr()->in('o.uid', $queryBuilder->createNamedParameter(
                    $this->replaceNewWithIds(array_keys($dataHandler->datamap['tx_reserve_domain_model_order'])),
                    Connection::PARAM_INT_ARRAY
                )))
                ->groupBy('f.uid');
            foreach ($queryBuilder->execute()->fetchAll() as $row) {
                CacheUtility::clearPageCachesForPagesWithCurrentFacility((int)$row['uid']);
                $facilityNames[] = $row['name'];
            }
        } elseif (array_key_exists('tx_reserve_domain_model_period', $dataHandler->datamap)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_reserve_domain_model_order');
            $queryBuilder
                ->select('f.uid', 'f.name')
                ->from('tx_reserve_domain_model_period', 'p')
                ->leftJoin('p', 'tx_reserve_domain_model_facility', 'f', 'p.facility = f.uid')
                ->where($queryBuilder->expr()->in('p.uid', $queryBuilder->createNamedParameter(
                    $this->replaceNewWithIds(array_keys($dataHandler->datamap['tx_reserve_domain_model_period'])),
                    Connection::PARAM_INT_ARRAY
                )))
                ->groupBy('f.uid');
            foreach ($queryBuilder->execute()->fetchAll() as $row) {
                CacheUtility::clearPageCachesForPagesWithCurrentFacility((int)$row['uid']);
                $facilityNames[] = $row['name'];
            }
        } elseif (array_key_exists('tx_reserve_domain_model_reservation', $dataHandler->datamap)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_reserve_domain_model_order');
            $queryBuilder
                ->select('f.uid', 'f.name')
                ->from('tx_reserve_domain_model_reservation', 'r')
                ->leftJoin('r', 'tx_reserve_domain_model_order', 'o', 'r.customer_order = o.uid')
                ->leftJoin('o', 'tx_reserve_domain_model_period', 'p', 'o.booked_period = p.uid')
                ->leftJoin('p', 'tx_reserve_domain_model_facility', 'f', 'p.facility = f.uid')
                ->where($queryBuilder->expr()->in('r.uid', $queryBuilder->createNamedParameter(
                    $this->replaceNewWithIds(array_keys($dataHandler->datamap['tx_reserve_domain_model_reservation'])),
                    Connection::PARAM_INT_ARRAY
                )))
                ->groupBy('f.uid');
            foreach ($queryBuilder->execute()->fetchAll() as $row) {
                CacheUtility::clearPageCachesForPagesWithCurrentFacility((int)$row['uid']);
                $facilityNames[] = $row['name'];
            }
        }
        if (!empty($facilityNames)) {
            /** @var FlashMessageQueue $flashMessageQueue */
            $flashMessageQueue = GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier();
            /** @var FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
                LocalizationUtility::translate('flashMessage.clearedCacheForFacility', 'reserve', [implode(', ', $facilityNames)]), '', FlashMessage::INFO);
            $flashMessageQueue->addMessage($flashMessage);
            return true;
        }
        return false;
    }

    protected function replaceNewWithIds(array $ids): array
    {
        foreach ($ids as &$id) {
            if (is_string($id) && strpos($id, 'NEW') === 0) {
                $id = $this->dataHandler->substNEWwithIDs[$id];
            }
        }
        return $ids;
    }
}
