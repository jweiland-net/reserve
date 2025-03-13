<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Controller;

use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Domain\Repository\PeriodRepository;
use JWeiland\Reserve\Domain\Repository\ReservationRepository;
use JWeiland\Reserve\Service\DataTablesService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ManagementController extends ActionController
{
    public function __construct(
        protected readonly PeriodRepository $periodRepository,
        protected readonly ReservationRepository $reservationRepository
    ) {}

    protected function initializeView($view): void
    {
        $view->assign('jsConf', [
            'datatables' => GeneralUtility::makeInstance(DataTablesService::class)->getConfiguration(),
            'language' => [
                'loading_video' => LocalizationUtility::translate('loading_video', 'reserve'),
                'status' => [
                    'code_not_found' => [
                        'title' => LocalizationUtility::translate('scan.status.error.1', 'reserve'),
                        'message' => LocalizationUtility::translate('code_not_found', 'reserve'),
                    ],
                ],
                'close' => LocalizationUtility::translate('close', 'reserve'),
                'reservations' => LocalizationUtility::translate('reservations', 'reserve'),
            ],
        ]);
    }

    public function overviewAction(): ResponseInterface
    {
        $this->view->assign(
            'periods',
            $this->periodRepository->findUpcomingAndRunningByFacilityUids(
                [(int)$this->settings['facility']],
            ),
        );

        return $this->htmlResponse();
    }

    public function scannerAction(Period $period): ResponseInterface
    {
        $this->view->assign('period', $period);

        return $this->htmlResponse();
    }

    public function periodAction(Period $period): ResponseInterface
    {
        $this->view->assign('period', $period);

        return $this->htmlResponse();
    }

    public function periodsOnSameDayAction(Period $period): ResponseInterface
    {
        $this->view->assign(
            'periods',
            $this->periodRepository->findByDate(
                $period->getDate(),
                (int)$this->settings['facility'],
            ),
        );

        return $this->htmlResponse();
    }

    public function scanAction(Reservation $reservation, bool $entireOrder = false): ResponseInterface
    {
        $view = $this->getJsonView();
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

        $reservations = $reservation->getCustomerOrder()->getReservations();

        $codes = [];

        if ($entireOrder) {
            foreach ($reservations as $otherReservations) {
                $otherReservations->setUsed(true);
                $codes[] = $otherReservations->getCode();
            }
        }

        $view->assign(
            'status',
            [
                'status' => [
                    'code' => $statusCode,
                    'title' => LocalizationUtility::translate('scan.status.error.' . $error, 'reserve'),
                    'message' => LocalizationUtility::translate('scan.status.' . $statusCode, 'reserve'),
                    'error' => $error,

                ],
                'codes' => $codes,
            ],
        );

        return $this->jsonResponse($view->render());
    }

    protected function getJsonView(): JsonView
    {
        return GeneralUtility::makeInstance(JsonView::class);
    }
}
