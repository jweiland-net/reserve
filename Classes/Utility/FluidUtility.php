<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Utility methods to configure the StandaloneView
 */
class FluidUtility
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected static $configurationManager;

    public static function configureStandaloneViewForMailing(StandaloneView $standaloneView): void
    {
        $extbaseFrameworkConfiguration = static::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'reserve',
            'Reservation'
        );
        // Mail templates can be overridden using the standard Extbase way plugin.tx_reserve.templateRootPaths ...
        // $extbaseFrameworkConfiguration is filled only if the default TypoScript setup is included, oterhwise
        // $extbaseFrameworkConfiguration['view']['templateRootPaths'] would be null and throw an exception so let's
        // set some default values!
        $standaloneView->setTemplateRootPaths(
            $extbaseFrameworkConfiguration['view']['templateRootPaths']
            ?? ['EXT:reserve/Resources/Private/Templates/']
        );
        $standaloneView->setLayoutRootPaths(
            $extbaseFrameworkConfiguration['view']['layoutRootPaths']
            ?? ['EXT:reserve/Resources/Private/Layouts/']
        );
        $standaloneView->setPartialRootPaths(
            $extbaseFrameworkConfiguration['view']['partialRootPaths']
            ?? ['EXT:reserve/Resources/Private/Partials/']
        );
        $standaloneView->getRenderingContext()->setControllerName('Mail');
    }

    public static function getConfigurationManager(): ConfigurationManagerInterface
    {
        if (static::$configurationManager === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$configurationManager = $objectManager->get(ConfigurationManager::class);
        }
        return static::$configurationManager;
    }

    /**
     * @param string $marker content to replace e.g. ###MY_MARKER###
     * @param string $template fluid template name lowercase!
     * @param string $content string which may contain $marker
     * @param array $vars additional vars for the fluid template
     * @return string
     */
    public static function replaceMarkerByRenderedTemplate(
        string $marker,
        string $template,
        string $content,
        array $vars = []
    ): string {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        static::configureStandaloneViewForMailing($standaloneView);
        $standaloneView->assignMultiple($vars);
        $standaloneView->setTemplate($template);
        return str_replace($marker, $standaloneView->render(), $content);
    }
}
