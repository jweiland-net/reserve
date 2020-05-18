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

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PeriodRepository extends Repository
{
    /**
     * @param int $uid
     * @return QueryResultInterface
     */
    public function findByFacility(int $uid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->equals('facility', $uid)
        );
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    public function findUpcomingAndRunningByFacility(int $uid): QueryResultInterface
    {
        $todayMidnight = new \DateTime('today midnight');
        $currentTime = new \DateTime('now');
        $currentTime->setDate(1970, 1, 1);
        $query = $this->findByFacility($uid)->getQuery();
        $query->matching(
            $query->logicalAnd(
                $query->getConstraint(),
                $query->greaterThanOrEqual('date', $todayMidnight->getTimestamp()),
                $query->greaterThanOrEqual('end', $currentTime->getTimestamp())
            )
        );
        return $query->execute();
    }
}
