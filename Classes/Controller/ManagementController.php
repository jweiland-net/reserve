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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
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

    protected function initializeView(ViewInterface $view)
    {
        $dataTablesLanguageFile = ExtensionManagementUtility::siteRelPath('reserve') . 'Resources/Public/JavaScript/datatables/' . $GLOBALS['TSFE']->sys_language_isocode . '.json';

        $view->assign('jsConf', [
            'datatables' => [
                'language' => [
                    'url' => file_exists($dataTablesLanguageFile) ? '/' . $dataTablesLanguageFile : ''
                ]
            ]
        ]);
    }

    public function overviewAction()
    {
        $this->view->assign('periods', $this->periodRepository->findByFacility((int)$this->settings['facility']));
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     */
    public function scannerAction(Period $period)
    {
        $this->view->assign('period', $period);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Period $period
     */
    public function periodAction(Period $period)
    {
        $this->view->assign('period', $period);
    }

    /**
     * @param \JWeiland\Reserve\Domain\Model\Reservation $reservation
     * @return mixed
     */
    public function scanAction(Reservation $reservation)
    {
        $view = $this->objectManager->get(JsonView::class);

        $view->setControllerContext($this->controllerContext);
        $view->setVariablesToRender(['status']);

        $error = 0;
        if (!$reservation->getCustomerOrder()->isActivated()) {
            $statusCode = 'orderNotActive';
            $error = 1;
        } elseif ($reservation->isUsed()) {
            $statusCode = 'codeAlreadyScanned';
            $error = 1;
        } else {
            $reservation->setUsed(true);
            $this->reservationRepository->update($reservation);

            $statusCode = 'reservationActivated';
        }

        $view->assign(
            'status',
            [
                'status' => [
                    'code' => $statusCode,
                    'title' => LocalizationUtility::translate('scan.status.error.' . $error, 'reserve'),
                    'message' => LocalizationUtility::translate('scan.status.' . $statusCode, 'reserve'),
                    'error' => $error
                ]
            ]
        );

        return $view->render();
    }
}
