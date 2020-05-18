<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
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

    public static function configureStandaloneViewForMailing(StandaloneView $standaloneView)
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
            $extbaseFrameworkConfiguration['view']['layoutRootPaths']
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
}
