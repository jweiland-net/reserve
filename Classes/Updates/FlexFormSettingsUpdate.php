<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Updates;

use Doctrine\DBAL\Exception;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * This UpgradeWizard fixes the typo in the flexform settings (changing disableDoupleOptin to disableDoubleOptin)
 */
#[UpgradeWizard('reserve_flexFormSettingsTypoUpdate')]
class FlexFormSettingsUpdate implements UpgradeWizardInterface
{
    use LoggerAwareTrait;

    public function getTitle(): string
    {
        return '[reserve] Fix typo in Reserve FlexForms: disableDoupleOptin -> disableDoubleOptin';
    }

    public function getDescription(): string
    {
        return 'Migrates the setting for double opt-in to the correct spelling to ensure settings are not lost.';
    }

    public function executeUpdate(): bool
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tt_content');
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();

        // Find records with the reserve plugin that contain the typo errors
        try {
            $records = $queryBuilder
                ->select('uid', 'pi_flexform')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->like(
                            'pi_flexform',
                            $queryBuilder->createNamedParameter('%disableDoupleOptin%'),
                        ),
                        $queryBuilder->expr()->eq(
                            'CType',
                            $queryBuilder->createNamedParameter('reserve_reservation'),
                        ),
                    ),
                )
                ->executeQuery()
                ->fetchAllAssociative();

            foreach ($records as $record) {
                // If column contains NULL, str_replace will throw warning.
                if (!is_string($record['pi_flexform'])) {
                    continue;
                }

                $newFlexForm = str_replace('disableDoupleOptin', 'disableDoubleOptin', $record['pi_flexform']);
                $connection->update(
                    'tt_content',
                    ['pi_flexform' => $newFlexForm],
                    ['uid' => (int)$record['uid']],
                );
            }
        } catch (Exception $e) {
            $this->logger->error(
                'FlexFormSettingsUpdate failed',
                ['exception' => $e],
            );

            return false;
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tt_content');
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();

        try {
            $count = $queryBuilder
                ->count('uid')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->like(
                            'pi_flexform',
                            $queryBuilder->createNamedParameter('%disableDoupleOptin%'),
                        ),
                        $queryBuilder->expr()->eq(
                            'CType',
                            $queryBuilder->createNamedParameter('reserve_reservation'),
                        ),
                    ),
                )
                ->executeQuery()
                ->fetchOne();
        } catch (Exception $e) {
            $this->logger->error(
                'FlexFormSettingsUpdate failed',
                ['exception' => $e],
            );

            return false;
        }

        return (int)$count > 0;
    }

    /**
     * @return array<class-string<DatabaseUpdatedPrerequisite>>
     */
    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    private function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
