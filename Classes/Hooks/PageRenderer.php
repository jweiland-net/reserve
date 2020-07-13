<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Hooks;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageRenderer
{
    const MODAL_SESSION_KEY = 'tx_reserve_modal';

    /**
     * Check the setting tx_reserve_modal (self::MODAL_UC_KEY) and add all necessary data
     * using the information from current BE_USER. Then remove the configuration when the
     * current settings are processed.
     *
     * @param array $params
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function processTxReserveModalUserSetting(array $params, \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        if (
            TYPO3_MODE === 'BE'
            && $this->getBackendUserAuthentication()->user
            && $configuration = $this->getBackendUserAuthentication()->getSessionData(self::MODAL_SESSION_KEY)
        ) {
            /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
            $pageRenderer->addJsInlineCode(
                'Require-JS-Module-TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule',
                'require(["TYPO3/CMS/Reserve/Backend/AskForMailAfterEditModule"]);'
            );

            foreach ($configuration['jsInlineCode'] as $name => $block) {
                $pageRenderer->addJsInlineCode($name, $block);
            }
            foreach ($configuration['inlineSettings'] as $namespace => $array) {
                $pageRenderer->addInlineSettingArray($namespace, $array);
            }
            foreach ($configuration['inlineLanguageLabel'] as $namespace => $value) {
                $pageRenderer->addInlineLanguageLabel($namespace, $value);
            }

            // remove modal configuration of current modal
            $this->getBackendUserAuthentication()->setAndSaveSessionData(self::MODAL_SESSION_KEY, null);
        }
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
