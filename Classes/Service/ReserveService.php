<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Official public API class for ext:reserve
 *
 * @api
 */
class ReserveService implements SingletonInterface
{
    /**
     * @var PeriodRepository
     */
    protected $periodRepository;

    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }

    /**
     * @param \DateTime $dateTime DateTime that contains the date of field "date" and time of field "begin"
     */
    public function findPeriodsByDateAndBegin(int $facilityUid, \DateTime $dateTime): array
    {
        return $this->periodRepository->findByDateAndBegin($dateTime, $facilityUid)->toArray();
    }

    /**
     * @param \DateTime $dateTimeOfPeriod DateTime that contains the date of field "date" and time of field "begin"
     */
    public function getRemainingParticipants(int $facilityUid, \DateTime $dateTimeOfPeriod): ?int
    {
        $periods = $this->findPeriodsByDateAndBegin($facilityUid, $dateTimeOfPeriod);
        $remainingParticipants = null;
        if ($periods) {
            $remainingParticipants = reset($periods)->getRemainingParticipants();
        }

        return $remainingParticipants;
    }
}
