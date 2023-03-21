<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DataTablesService
{
    /**
     * Get configuration for DataTables config array. Assign it to the frontend.
     *
     * @return array|string[]
     */
    public function getConfiguration(): array
    {
        $configuration = [];

        if ($url = LocalizationUtility::translate('datatables.languageFile', 'reserve')) {
            $configuration['language'] = [
                'url' => PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName($url)),
            ];
        }

        return $configuration;
    }
}
