<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PeriodRepository extends Repository
{
    protected $defaultOrderings = [
        'date' => QueryInterface::ORDER_ASCENDING,
        'begin' => QueryInterface::ORDER_ASCENDING,
        'end' => QueryInterface::ORDER_ASCENDING
    ];

    public function __construct(ObjectManagerInterface $objectManager)
    {
        parent::__construct($objectManager);
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param array $uid
     * @return QueryResultInterface
     */
    public function findByFacilityUids(array $uids): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->in('facility', $uids)
        );
        return $query->execute();
    }

    /**
     * @param \DateTime $uid
     * @return QueryResultInterface
     */
    public function findByDate(\DateTime $date, int $facilityUid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->logicalAnd(
                $query->equals('date', $date->getTimestamp()),
                $query->equals('facility', $facilityUid)
            )
        );
        return $query->execute();
    }

    public function findByDateAndBegin(\DateTime $dateTime, int $facilityUid): QueryResultInterface
    {
        $date = clone $dateTime;
        $date->setTime(0, 0);
        // $begin must be UTC because TCA saves that timestamp in UTC but others in configured timezone
        $begin = new \DateTime('1970-01-01T%d:%d:%dZ', GeneralUtility::intExplode(':', $dateTime->format('H:i:s')));
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('facility', $facilityUid),
                $query->equals('date', $date->getTimestamp()),
                $query->equals('begin', $begin->getTimestamp())
            )
        );
        return $query->execute();
    }

    public function findUpcomingAndRunningByFacilityUids(array $uids): QueryResultInterface
    {
        $todayMidnight = new \DateTime('today midnight');
        $currentTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $currentTime->setDate(1970, 1, 1);
        $query = $this->findByFacilityUids($uids)->getQuery();
        $query->matching(
            $query->logicalAnd(
                $query->getConstraint(),
                $query->logicalOr(
                    $query->greaterThanOrEqual('date', (new \DateTime('tomorrow'))->getTimestamp()),
                    $query->logicalAnd(
                        $query->equals('date', $todayMidnight->getTimestamp()),
                        $query->greaterThanOrEqual('end', $currentTime->getTimestamp())
                    )
                )
            )
        );
        $query->setOrderings(
            [
                'date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
                'begin' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ]
        );
        return $query->execute();
    }
}
