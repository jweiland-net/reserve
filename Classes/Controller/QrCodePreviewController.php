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

namespace JWeiland\Reserve\Controller\Backend;

use JWeiland\Reserve\Domain\Model\Facility;
use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Model\Period;
use JWeiland\Reserve\Domain\Model\Reservation;
use JWeiland\Reserve\Domain\Repository\FacilityRepository;
use JWeiland\Reserve\Utility\QrCodeUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Controller to render an example QR code based on a selected facility
 * Route: tx_reserve_example_qr_code
 */
class QrCodePreviewController
{
    public function __construct()
    {
        /** @var $GLOBALS['LANG'] anguageService */
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
        $GLOBALS['LANG']->init($GLOBALS['BE_USER']->uc['lang']);
    }

    public function ajaxAction(ServerRequestInterface $request)
    {
        $facilityUid = (int)($request->getQueryParams()['facility'] ?? 0);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $data = ['hasErrors' => false, 'message' => '', 'qrCode' => ''];
        if ($facilityUid) {
            $facility = $objectManager->get(FacilityRepository::class)->findByUid((int)$facilityUid);
            if ($facility instanceof Facility) {
                /** @var Reservation $reservation */
                $reservation = GeneralUtility::makeInstance(Reservation::class);
                $reservation->setCode('an-example-' . time());
                /** @var Order $order */
                $order = GeneralUtility::makeInstance(Order::class);
                $order->getReservations()->attach($reservation);
                /** @var Period $period */
                $period = GeneralUtility::makeInstance(Period::class);
                $period->setBegin(new \DateTime('today 10 am'));
                $period->setEnd(new \DateTime('today 2 pm'));
                $period->setDate(new \DateTime('today midnight'));
                $period->setBookingBegin(new \DateTime('yesterday 10 am'));
                $period->setFacility($facility);
                $order->setBookedPeriod($period);
                $reservation->setCustomerOrder($order);
                $data['qrCode'] = QrCodeUtility::generateQrCode($reservation)->writeDataUri();
            } else {
                $data['hasErrors'] = true;
                $data['message'] = 'Could not find facility!';
            }
        } else {
            $data['hasErrors'] = true;
            $data['message'] = 'You have to provide the facility uid with param \'facility\'!';
        }
        // replace this by JsonResponse as soon as TYPO3 v8 is removed from requirements!
        $response = new Response('php://temp', 200, ['Content-Type' => 'application/json; charset=utf-8']);
        $response->getBody()->write(json_encode($data));
        return $response;
    }
}
