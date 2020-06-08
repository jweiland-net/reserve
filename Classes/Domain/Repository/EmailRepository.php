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

namespace JWeiland\Reserve\Domain\Repository;

use JWeiland\Reserve\Domain\Model\Email;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class EmailRepository extends Repository
{
    /**
     * @return QueryResultInterface
     */
    public function findAll(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    /**
     * @return Email|null
     */
    public function findOneUnlocked()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('locked', false));
        return $query->execute()->getFirst();
    }

    /**
     * @param int $uid
     * @param Email|null $email optional to set "locked" property in ExtBase domain model
     */
    public function lockEmail(int $uid, Email $email = null)
    {
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_reserve_domain_model_email');
        $connection->update('tx_reserve_domain_model_email', ['locked' => true], ['uid' => $uid]);
        if ($email) {
            $email->setLocked(true);
        }
    }
}
