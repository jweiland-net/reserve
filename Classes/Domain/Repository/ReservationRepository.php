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

class ReservationRepository extends Repository
{
    /**
     * @param string $code
     * @return QueryResultInterface
     */
    public function findByCode(string $code): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->equals('code', $code)
        );
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }
}
