<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Repository;

use JWeiland\Reserve\Domain\Model\Order;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class OrderRepository extends Repository
{
    private const TABLE = 'tx_reserve_domain_model_order';

    public function findByEmailAndActivationCode(string $email, string $activationCode): ?Order
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('email', $email),
                $query->equals('activationCode', $activationCode),
            )
        );

        return $query->execute()->getFirst();
    }

    /**
     * @return QueryResultInterface|Order[]
     * @throws InvalidQueryException
     */
    public function findInactiveOrders(int $olderThanInSeconds): QueryResultInterface
    {
        $olderThan = new \DateTime();
        $olderThan->modify('-' . $olderThanInSeconds . 'seconds');

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('activated', 0),
                $query->lessThan('crdate', $olderThan->getTimestamp()),
            )
        );

        return $query->execute();
    }

    /**
     * @param array|string[] $selects
     * @param int|null $maxResults The maximum number of results to retrieve or NULL to retrieve all results
     * @throws \Exception
     * @internal
     */
    protected function findWherePeriodEndedQueryBuilder(
        int $endedSinceSeconds,
        array $selects = ['o.*'],
        ?int $maxResults = null
    ): QueryBuilder {
        $dateTime = new \DateTime('now');
        $dateTime->modify('-' . $endedSinceSeconds . 'seconds');

        $periodDate = new \DateTime(sprintf('%s 0:0:0', $dateTime->format('Y-m-d')), new \DateTimeZone('UTC'));
        $periodEnd = new \DateTime(sprintf('1970-01-01 %s', $dateTime->format('H:i:s')), new \DateTimeZone('UTC'));
        unset($dateTime);

        $end = new \DateTime('now');
        $end
            ->setTimezone(new \DateTimeZone(date_default_timezone_get()))
            ->modify('-' . $endedSinceSeconds . 'seconds')
            ->setDate(1970, 1, 1);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder
            ->select(...$selects)
            ->from(self::TABLE, 'o')
            ->leftJoin(
                'o',
                'tx_reserve_domain_model_period',
                'p',
                'o.booked_period = p.uid'
            )
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->or(
                        // not less than equal because this would remove events without respecting the field "end"
                        // days before the calculated day
                        $queryBuilder->expr()->lt(
                            'p.date',
                            $queryBuilder->createNamedParameter($periodDate->getTimestamp())
                        ),
                        $queryBuilder->expr()->and(
                            // calculated day AND calculated end time
                            $queryBuilder->expr()->eq(
                                'p.date',
                                $queryBuilder->createNamedParameter($periodDate->getTimestamp())
                            ),
                            $queryBuilder->expr()->lte(
                                'p.end',
                                $queryBuilder->createNamedParameter($periodEnd->getTimestamp())
                            )
                        )
                    )
                )
            )
            ->setMaxResults($maxResults);

        return $queryBuilder;
    }

    /**
     * @param array|string[] $selects
     * @param int|null $maxResults The maximum number of results to retrieve or NULL to retrieve all results
     *
     * @throws \Exception
     */
    public function findWherePeriodEndedRaw(
        int $endedSinceSeconds,
        array $selects = ['o.*'],
        ?int $maxResults = null
    ): array {
        return $this
            ->findWherePeriodEndedQueryBuilder($endedSinceSeconds, $selects, $maxResults)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param int|null $maxResults The maximum number of results to retrieve or NULL to retrieve all results
     *
     * @return QueryResultInterface|Order[]
     *
     * @throws Exception
     */
    public function findWherePeriodEnded(
        int $endedSinceSeconds,
        array $selects = ['o.*'],
        ?int $maxResults = null
    ): QueryResultInterface {
        /** @var Query $query */
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->statement($this->findWherePeriodEndedQueryBuilder($endedSinceSeconds, $selects, $maxResults));

        return $query->execute();
    }
}
