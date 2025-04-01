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
use JWeiland\Reserve\Event\SendCancellationEmailEvent;
use JWeiland\Reserve\Utility\CacheUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Service to cancel an order
 */
class CancellationService implements SingletonInterface
{
    private const REASON_CUSTOMER = 'customer';

    public const REASON_INACTIVE = 'inactive';

    public function __construct(
        private readonly FluidService $fluidService,
        private readonly DataHandler $dataHandler,
        private readonly EventDispatcher $eventDispatcher,
        private readonly MailService $mailService,
    ) {
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

            $this->mailService->sendMailToCustomer(
                $order,
                LocalizationUtility::translate('mail.cancellation.subject', 'reserve'),
                $view->render(),
                function (array $data, string $subject, string $bodyHtml, MailMessage $mailMessage) {
                    foreach ($data['order']->getReservations() as $reservation) {
                        /** @var SendCancellationEmailEvent $event */
                        $event = $this->eventDispatcher->dispatch(
                            new SendCancellationEmailEvent($mailMessage),
                        );
                        $mailMessage = $event->getMailMessage();
                    }
                }
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
