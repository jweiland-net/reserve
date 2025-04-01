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
use JWeiland\Reserve\Utility\FluidUtility;
use JWeiland\Reserve\Utility\OrderSessionUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
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

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var MailService
     */
    protected $mailService;

    public function __construct(
        PersistenceManager $persistenceManager,
        EventDispatcher $eventDispatcher,
        MailService $mailService
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailService = $mailService;
    }

    /**
     * @param string $reason use CancellationService::REASON_ constants or add your own reason
     * @param array $vars additional variables that will be assigned to the fluid template
     * @param bool $sendMailToCustomer set false to cancel the order without sending a mail to the customer
     * @param bool $persist set false to persist the order by yourself using $cancellationService->getPersistenceManager()->persistAll()
     */
    public function cancel(
        Order $order,
        string $reason = self::REASON_CUSTOMER,
        array $vars = [],
        bool $sendMailToCustomer = true,
        bool $persist = true
    ): void {
        $this->persistenceManager->remove($order);
        if ($sendMailToCustomer) {
            $view = $this->getStandaloneView();
            FluidUtility::configureStandaloneViewForMailing($view);
            $view->assignMultiple(array_merge(['order' => $order, 'reason' => $reason], $vars));
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

        if ($persist) {
            $this->persistenceManager->persistAll();
            CacheUtility::clearPageCachesForPagesWithCurrentFacility($order->getBookedPeriod()->getFacility()->getUid());
        }

        OrderSessionUtility::unblockNewOrdersForFacilityInCurrentSession(
            $order->getBookedPeriod()->getFacility()->getUid()
        );
    }

    public function getStandaloneView(): StandaloneView
    {
        return GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * ToDo: SF: This should not be public.
     * Currently used from RemoveInactiveOrdersCommand
     * Should be set to private or migrated to Command
     */
    public function getPersistenceManager(): PersistenceManager
    {
        return $this->persistenceManager;
    }
}
