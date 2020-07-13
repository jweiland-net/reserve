<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Command;

use JWeiland\Reserve\Domain\Repository\OrderRepository;
use JWeiland\Reserve\Service\CancellationService;
use JWeiland\Reserve\Utility\CacheUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Command to remove inactive orders after a given expiration time
 */
class RemoveInactiveOrdersCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Remove inactive orders after a given time.');
        $this->setHelp('Remove inactive orders (orders with active = 0) after a given time.');
        $this->addOption('expiration-time', 't', InputOption::VALUE_OPTIONAL, 'Expiration time of an inactive order in seconds', '3600');
        $this->addOption(
            'locale',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Locale to be used inside templates and translations. Value that is available inside the Locales class '
            . '(TYPO3\\CMS\\Core\\Localization\\Locales). Example: "default" for english, "de" for german.',
            'default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $GLOBALS['LANG']->init((string)$input->getOption('locale'));
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var CancellationService $cancellationService */
        $cancellationService = $objectManager->get(CancellationService::class);
        /** @var OrderRepository $orderRepository */
        $orderRepository = $objectManager->get(OrderRepository::class);
        $inactiveOrders = $orderRepository->findInactiveOrders((int)$input->getOption('expiration-time'));
        $inactiveOrders->getQuery()->setLimit(30);
        $progressBar = new ProgressBar($output, $inactiveOrders->count());
        $progressBar->start();
        $affectedFacilities = [];
        foreach ($inactiveOrders as $inactiveOrder) {
            try {
                $affectedFacilities[$inactiveOrder->getBookedPeriod()->getFacility()->getUid()] = true;
                $cancellationService->cancel(
                    $inactiveOrder,
                    CancellationService::REASON_INACTIVE,
                    ['expirationTime' => $input->getOption('expiration-time')],
                    true,
                    false
                );
            } catch (\Throwable $exception) {
                $output->writeln('Could not cancel the order ' . $inactiveOrder->getUid() . ' using cancellation service!');
                // anyway make sure to remove the order!
                $cancellationService->getPersistenceManager()->remove($inactiveOrder);
            }
            $progressBar->advance(1);
        }
        $progressBar->finish();
        $cancellationService->getPersistenceManager()->persistAll();
        $output->writeln('Clear caches for affected facilities list views...');
        foreach ($affectedFacilities as $facilityUid => $_) {
            CacheUtility::clearPageCachesForPagesWithCurrentFacility($facilityUid);
        }
        return 0;
    }
}
