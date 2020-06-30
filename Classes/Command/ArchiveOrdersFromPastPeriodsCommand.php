<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Command;

use JWeiland\Reserve\Domain\Model\Order;
use JWeiland\Reserve\Domain\Repository\OrderRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Command to archive orders from past periods. This removes the email!
 */
class ArchiveOrdersFromPastPeriodsCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Archive orders from past periods.');
        $this->setHelp('Archive orders from past periods.');
        $this->addOption('ended-since', 'e', InputOption::VALUE_OPTIONAL, 'Time since the period ended in seconds', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(PersistenceManager::class);
        /** @var OrderRepository $orderRepository */
        $orderRepository = $objectManager->get(OrderRepository::class);
        $endedOrders = $orderRepository->findWherePeriodEnded((int)$input->getOption('ended-since'));
        $endedOrders->getQuery()->setLimit(30);
        $progressBar = new ProgressBar($output, $endedOrders->count());
        $progressBar->start();
        foreach ($endedOrders as $endedOrder) {
            $endedOrder->setOrderType(Order::TYPE_ARCHIVED);
            $endedOrder->setEmail('');
            $persistenceManager->add($endedOrder);
            $progressBar->advance();
        }
        $persistenceManager->persistAll();
        $progressBar->finish();
        return 0;
    }
}
