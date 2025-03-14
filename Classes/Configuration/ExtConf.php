<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reserve.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Reserve\Configuration;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Extension configuration for EXT:reserve
 */
#[Autoconfigure(constructor: 'create')]
final readonly class ExtConf
{
    private const EXT_KEY = 'reserve';

    private const DEFAULT_SETTINGS = [
        'blockMultipleOrdersInSeconds' => 3600,
        'disableQRCodeGeneration' => false,
    ];

    public function __construct(
        private int $blockMultipleOrdersInSeconds = self::DEFAULT_SETTINGS['blockMultipleOrdersInSeconds'],
        private bool $disableQRCodeGeneration = self::DEFAULT_SETTINGS['disableQRCodeGeneration'],
    ) {}

    public static function create(ExtensionConfiguration $extensionConfiguration): self
    {
        $extensionSettings = self::DEFAULT_SETTINGS;

        try {
            $extensionSettings = array_merge(
                $extensionSettings,
                $extensionConfiguration->get(self::EXT_KEY),
            );
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        }

        return new self(
            blockMultipleOrdersInSeconds: (int)$extensionSettings['blockMultipleOrdersInSeconds'],
            disableQRCodeGeneration: (bool)$extensionSettings['disableQRCodeGeneration'],
        );
    }

    public function getBlockMultipleOrdersInSeconds(): int
    {
        return $this->blockMultipleOrdersInSeconds;
    }

    public function getDisableQRCodeGeneration(): bool
    {
        return $this->disableQRCodeGeneration;
    }

    public function isQrCodeGenerationEnabled(): bool
    {
        return !$this->disableQRCodeGeneration;
    }
}
