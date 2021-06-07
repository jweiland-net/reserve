<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class FacilityRepository extends Repository
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
     * @param int[] $uids
     * @return QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByUids(array $uids): QueryResultInterface
    {
        $query = $this->createQuery();
        $query
            ->getQuerySettings()
            ->setRespectStoragePage(false);
        return $query->matching($query->in('uid', $uids))->execute();
    }
}
