<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Service to cancel an order
 */
class CancellationService implements SingletonInterface
{
    /**
     * Reasons for cancellation
     */
    private const REASON_CUSTOMER = 'customer';

    public const REASON_INACTIVE = 'inactive';

    protected FluidService $fluidService;

    protected DataHandler $dataHandler;

    public function __construct(FluidService $fluidService, DataHandler $dataHandler)
    {
        $this->fluidService = $fluidService;
        $this->dataHandler = $dataHandler;
    }

    /**
     * @param string $reason use CancellationService::REASON_ constants or add your own reason
     * @param array $vars additional variables that will be assigned to the fluid template
     * @param bool $sendMailToCustomer set false to cancel the order without sending a mail to the customer
     */
    public function cancel(
        Order $order,
        ServerRequestInterface $request,
        string $reason = self::REASON_CUSTOMER,
        array $vars = [],
        bool $sendMailToCustomer = true,
    ): void {
        if ($sendMailToCustomer) {
            $view = $this->getStandaloneView();

            $this->fluidService->configureStandaloneViewForMailing($view);

            $view->assignMultiple(['order' => $order, 'reason' => $reason]);
            $view->assignMultiple($vars);
            $view->setTemplate('Cancellation');

            $this
                ->getMailService()
                ->sendMailToCustomer(
                    $order,
                    LocalizationUtility::translate('mail.cancellation.subject', 'reserve'),
                    $view->render(),
                );
        }

        // Remove with DataHandler
        $this->dataHandler->start([], []);
        $this->dataHandler->deleteRecord('tx_reserve_domain_model_order', $order->getUid());
        $this->dataHandler->process_datamap();

        CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());

        OrderSessionUtility::unblockNewOrdersForFacilityInCurrentSession(
            $order->getBookedPeriod()->getFacility()->getUid(),
            $request,
        );
    }

    public function getStandaloneView(): StandaloneView
    {
        return GeneralUtility::makeInstance(StandaloneView::class);
    }

    protected function getMailService(): MailService
    {
        return GeneralUtility::makeInstance(MailService::class);
    }
}
