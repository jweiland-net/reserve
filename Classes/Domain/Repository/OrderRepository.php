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

use JWeiland\Reserve\Domain\Model\Order;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class OrderRepository extends Repository
{
    public function findByEmailAndActivationCode(string $email, string $activationCode)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('email', $email),
                $query->equals('activationCode', $activationCode)
            )
        );
        return $query->execute()->getFirst();
    }

    /**
     * @param int $olderThanInSeconds
     * @return QueryResultInterface|Order[]
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
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
                $query->lessThan('crdate', $olderThan->getTimestamp())
            )
        );
        return $query->execute();
    }
}
