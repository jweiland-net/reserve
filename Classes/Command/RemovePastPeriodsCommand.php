<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Remove past periods and all related records like orders, reservations, codes.
 */
class RemovePastPeriodsCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Remove past periods and all related records like orders, reservations, codes.');
        $this->setHelp('Remove past periods and all related records like orders, reservations, codes.');
        $this->addOption('ended-since', 'e', InputOption::VALUE_OPTIONAL, 'Time since the period ended in seconds', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTime('now');
        $dateTime->modify('-' . (int)$input->getOption('ended-since') . 'seconds');
        $periodDate = new \DateTime(sprintf('%s 0:0:0', $dateTime->format('Y-m-d')), new \DateTimeZone('UTC'));
        $periodEnd = new \DateTime(sprintf('1970-01-01 %s', $dateTime->format('H:i:s')), new \DateTimeZone('UTC'));

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_reserve_domain_model_period');
        $queryBuilder
            ->select('uid')
            ->from('tx_reserve_domain_model_period', 'p')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lte('p.date', $periodDate->getTimestamp()),
                    $queryBuilder->expr()->lte('p.end', $periodEnd->getTimestamp())
                )
            // One period can have much orders and one order can have much reservations
            // so a small limit is required to prevent running into timeouts
            )->setMaxResults(5);

        $cmd = ['tx_reserve_domain_model_period' => []];
        foreach ($queryBuilder->execute()->fetchAll() as $period) {
            $cmd['tx_reserve_domain_model_period'][$period['uid']]['delete'] = 1;
        }

        $GLOBALS['BE_USER']->backendCheckLogin();
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();

        if (!empty($dataHandler->errorLog)) {
            $output->writeln('Errors during DataHandler operations:');
            $output->writeln($dataHandler->errorLog);
            return 1;
        }

        return 0;
    }
}
