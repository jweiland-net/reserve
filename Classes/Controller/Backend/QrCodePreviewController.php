<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Controller\Backend;

use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Domain\Repository\FacilityRepository;
use JWeiland\Reserve\Utility\QrCodeUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller to render an example QR code based on a selected facility
 * Route: tx_reserve_example_qr_code
 */
class QrCodePreviewController
{
    protected FacilityRepository $facilityRepository;

    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG']->init($GLOBALS['BE_USER']->uc['lang']);
    }

    public function ajaxAction(ServerRequestInterface $request): JsonResponse
    {
        $facilityUid = (int)($request->getQueryParams()['facility'] ?? 0);
        $data = ['hasErrors' => false, 'message' => '', 'qrCode' => ''];
        if ($facilityUid !== 0) {
            $facility = $this->facilityRepository->findByUid($facilityUid);
            if ($facility instanceof Facility) {
                $reservation = $this->getEmptyReservation();
                $reservation->setCode('an-example-' . time());

                $order = $this->getEmptyOrder();
                $order->getReservations()->attach($reservation);

                $period = $this->getEmptyPeriod();
                $period->setBegin(new \DateTime('today 10 am'));
                $period->setEnd(new \DateTime('today 2 pm'));
                $period->setDate(new \DateTime('today midnight'));
                $period->setBookingBegin(new \DateTime('yesterday 10 am'));
                $period->setFacility($facility);

                $order->setBookedPeriod($period);

                $reservation->setCustomerOrder($order);

                $data['qrCode'] = QrCodeUtility::generateQrCode($reservation)->getDataUri();
            } else {
                $data['hasErrors'] = true;
                $data['message'] = 'Could not find facility!';
            }
        } else {
            $data['hasErrors'] = true;
            $data['message'] = "You have to provide the facility uid with param 'facility'!";
        }

        return new JsonResponse($data);
    }

    private function getEmptyReservation(): Reservation
    {
        return GeneralUtility::makeInstance(Reservation::class);
    }

    private function getEmptyOrder(): Order
    {
        return GeneralUtility::makeInstance(Order::class);
    }

    private function getEmptyPeriod(): Period
    {
        return GeneralUtility::makeInstance(Period::class);
    }
}
