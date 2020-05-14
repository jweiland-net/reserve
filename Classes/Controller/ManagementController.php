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

namespace JWeiland\Reserve\Controller;

use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Domain\Repository\ReservationRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ManagementController extends ActionController
{
    /**
     * @var PeriodRepository
     */
    private $periodRepository;
    /**
     * @var ReservationRepository
     */
    private $reservationRepository;

    public function __construct(PeriodRepository $periodRepository, ReservationRepository $reservationRepository)
    {
        $this->periodRepository = $periodRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function overviewAction()
    {
        $this->view->assign('periods', $this->periodRepository->findByFacility((int)$this->settings['facility']));
    }

    public function periodAction(Period $period)
    {
        $this->view->assign('period', $period);
    }

    public function scannerAction()
    {
        $this->view->assign('reservations', $this->reservationRepository->findAll());
    }
}
