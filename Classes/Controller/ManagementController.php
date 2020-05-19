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
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Domain\Repository\ReservationRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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

    /**
     * @param \JWeiland\Reserve\Domain\Repository\PeriodRepository $periodRepository
     * @param \JWeiland\Reserve\Domain\Repository\ReservationRepository $reservationRepository
     */
    public function __construct(PeriodRepository $periodRepository, ReservationRepository $reservationRepository)
    {
        $this->periodRepository = $periodRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function overviewAction()
    {
        $this->view->assign('periods', $this->periodRepository->findByFacility((int)$this->settings['facility']));
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     * @param string $code
     */
    public function periodAction(Period $period, string $code = '')
    {
        $this->view->assign('period', $period);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Reservation $reservation
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function scanAction(Reservation $reservation)
    {
        $view = $this->objectManager->get(JsonView::class);

        $view->setControllerContext($this->controllerContext);
        $view->setVariablesToRender(['status']);

        $statusCode = '';

        if (!$reservation->isUsed()) {
            $reservation->setUsed(true);
            $this->reservationRepository->update($reservation);
            $statusCode = 'activated';
        } else {
            $statusCode = 'active';
        }

        $statusMessage = LocalizationUtility::translate('scan.status.' . $statusCode, 'reserve');

        $view->assign('status', ['status' => ['code' => $statusCode, 'message' => $statusMessage]]);

        return $view->render();
    }
}
