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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Remove past periods and all related records like orders, reservations, codes.
 */
class RemovePastPeriodsCommand extends Command
{
    protected DataHandler $dataHandler;

    protected OrderRepository $orderRepository;

    public function injectDataHandler(DataHandler $dataHandler): void
    {
        $this->dataHandler = $dataHandler;
    }

    public function injectOrderRepository(OrderRepository $orderRepository): void
    {
        $this->orderRepository = $orderRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Remove past periods and all related records like orders, reservations, codes.');
        $this->setHelp(
            <<<HELP
By default this command removes all periods and related records from past periods.
Attention: This task removes the records using the DataHandler. This means that "remove" does not mean it is
removed from the database. The records are still in the database with a deleted flag.
You have to run the command `cleanup:deletedrecords` or scheduler task `Recycler: Remove deleted records`
to remove them permanently from the database!
HELP
        );
        $this->addOption(
            'ended-since',
            'e',
            InputOption::VALUE_OPTIONAL,
            'Time since the period ended in seconds',
            0
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $periods = $this->orderRepository->findWherePeriodEndedRaw(
            (int)$input->getOption('ended-since'),
            [
                'p.uid',
            ],
            5
        );

        $cmd = ['tx_reserve_domain_model_period' => []];
        foreach ($periods as $period) {
            $cmd['tx_reserve_domain_model_period'][$period['uid']]['delete'] = 1;
        }

        $GLOBALS['BE_USER']->backendCheckLogin();

        $this->dataHandler->start([], $cmd);
        $this->dataHandler->process_cmdmap();

        if (!empty($this->dataHandler->errorLog)) {
            $output->writeln('Errors during DataHandler operations:');
            $output->writeln($this->dataHandler->errorLog);
            return 1;
        }

        return 0;
    }
}
