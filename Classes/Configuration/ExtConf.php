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

    private const BLOCK_MULTIPLE_ORDERS_IN_SECONDS = 3600;

    private const DISABLE_QR_CODE_GENERATION = false;

    private const DEFAULT_SETTINGS = [
        'blockMultipleOrdersInSeconds' => 3600,
        'disableQRCodeGeneration' => false,
    ];

    public function __construct(
        private int $blockMultipleOrdersInSeconds = self::BLOCK_MULTIPLE_ORDERS_IN_SECONDS,
        private int $disableQRCodeGeneration = self::DISABLE_QR_CODE_GENERATION,
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
            disableQRCodeGeneration: (int)$extensionSettings['disableQRCodeGeneration'],
        );
    }

    public function getBlockMultipleOrdersInSeconds(): int
    {
        return $this->blockMultipleOrdersInSeconds;
    }

    public function getDisableQRCodeGeneration(): int
    {
        return $this->disableQRCodeGeneration;
    }

    public function isQrCodeGenerationEnabled(): bool
    {
        return !$this->disableQRCodeGeneration;
    }
}
