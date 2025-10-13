<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Repository;

use Doctrine\DBAL\Exception;
use JWeiland\Reserve\Domain\Model\Email;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class EmailRepository extends Repository
{
    private const TABLE = 'tx_reserve_domain_model_email';

    public function __construct(
        protected readonly ConnectionPool $connectionPool,
    ) {}

    public function findAll(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    /**
     * @throws Exception
     */
    public function findUnlockedEmails(): array
    {
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable(self::TABLE);

        return $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('locked', $queryBuilder->createNamedParameter(false, Connection::PARAM_BOOL)),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findOneUnlocked(): ?Email
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('locked', false));

        return $query->execute()->getFirst();
    }

    /**
     * @param Email|null $email optional to set "locked" property in ExtBase domain model
     */
    public function lockEmail(int $uid, ?Email $email = null): void
    {
        $this
            ->getConnectionForTable(self::TABLE)
            ->update(
                self::TABLE,
                [
                    'locked' => true,
                ],
                [
                    'uid' => $uid,
                ],
            );

        if ($email instanceof Email) {
            $email->setLocked(true);
        }
    }

    protected function getConnectionForTable(string $table): Connection
    {
        return $this->getConnectionPool()->getConnectionForTable($table);
    }

    protected function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
