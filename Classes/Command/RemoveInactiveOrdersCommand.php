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
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Command to remove inactive orders after a given expiration time
 */
class RemoveInactiveOrdersCommand extends Command
{
    public function __construct(
        private readonly CancellationService $cancellationService,
        private readonly OrderRepository $orderRepository,
        private readonly SiteFinder $siteFinder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Remove inactive orders after a given time.');
        $this->setHelp('Remove inactive orders (orders with active = 0) after a given time.');

        $this->addOption(
            'expiration-time',
            't',
            InputOption::VALUE_OPTIONAL,
            'Expiration time of an inactive order in seconds',
            '3600',
        );
        $this->addOption(
            'locale',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Locale to be used inside templates and translations. Value that is available inside the Locales class '
            . '(TYPO3\\CMS\\Core\\Localization\\Locales). Example: "default" for english, "de" for german.',
            'default',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $GLOBALS['LANG']->init((string)$input->getOption('locale'));
        $inactiveOrders = $this->orderRepository->findInactiveOrders(
            (int)$input->getOption('expiration-time'),
        );

        $progressBar = new ProgressBar($output, count($inactiveOrders));
        $progressBar->start();

        $affectedFacilities = [];
        foreach ($inactiveOrders as $inactiveOrder) {
            try {
                Bootstrap::initializeBackendAuthentication();

                // The site has to have a fully qualified domain name
                $site = $this->siteFinder->getSiteByPageId(1);

                $request = (new ServerRequest())
                    ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
                    ->withAttribute('site', $site);
                $GLOBALS['TYPO3_REQUEST'] = $request;

                $facilityUids = $this->orderRepository->findAffectedFacilities($inactiveOrder['uid']);
                $affectedFacilities = array_fill_keys($facilityUids, true);
                $affectedFacilities[$inactiveOrder->getBookedPeriod()->getFacility()->getUid()] = true;
                $this->cancellationService->cancel(
                    $inactiveOrder,
                    $this->request,
                    CancellationService::REASON_INACTIVE,
                    ['expirationTime' => $input->getOption('expiration-time')],
                    true,
                );
            } catch (\Throwable $exception) {
                $output->writeln('Could not cancel the order ' . $inactiveOrder['uid'] . ' using cancellation service!');
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('Clear caches for affected facilities list views...');

        foreach (array_keys($affectedFacilities) as $facilityUid) {
            CacheUtility::clearPageCachesForPagesWithCurrentFacility($facilityUid);
        }

        return 0;
    }
}
