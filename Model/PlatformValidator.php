<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Model;

use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Comparison;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;
use InvalidArgumentException;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ProductMetadataInterfaceFactory;

class PlatformValidator
{
    /**
     * @var ProductMetadataInterface|null
     */
    private ?ProductMetadataInterface $productMetadata = null;

    /**
     * @param ProductMetadataInterfaceFactory $productMetadataFactory
     * @param NullCache $nullCache
     */
    public function __construct(
        private readonly ProductMetadataInterfaceFactory $productMetadataFactory,
        private readonly NullCache $nullCache
    ) {
    }

    /**
     * Check whether the current platform satisfies a condition.
     *
     * @param Platform $platform
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isFit(Platform $platform): bool
    {
        $this->assertValid($platform);

        return $this->isEditionFit($platform->edition)
            && $this->isDistributionFit($platform->distribution)
            && $this->isVersionFit($platform->version, $platform->comparison)
            && $this->isDistributionVersionFit($platform->distributionVersion, $platform->comparison);
    }

    /**
     * Reject a condition that can never be evaluated correctly.
     *
     * @param Platform $platform
     * @return void
     * @throws InvalidArgumentException
     */
    private function assertValid(Platform $platform): void
    {
        if ($platform->version === '' || $platform->distributionVersion === '') {
            throw new InvalidArgumentException('Platform versions must not be empty.');
        }

        if ($platform->distributionVersion !== null && $platform->distribution !== Distribution::MageOs) {
            throw new InvalidArgumentException('Platform distributionVersion requires Distribution::MageOs.');
        }
    }

    /**
     * Check the declared edition against the installed product.
     *
     * @param Edition $edition
     * @return bool
     */
    private function isEditionFit(Edition $edition): bool
    {
        return $edition === Edition::Any || $this->getProductMetadata()->getEdition() === $edition->value;
    }

    /**
     * Check the declared distribution against the installed product.
     *
     * @param Distribution $distribution
     * @return bool
     */
    private function isDistributionFit(Distribution $distribution): bool
    {
        return match ($distribution) {
            Distribution::Any => true,
            Distribution::MageOs => $this->isMageOs(),
            Distribution::Magento => !$this->isMageOs(),
        };
    }

    /**
     * Check the declared Magento version against the installed product.
     *
     * @param string|null $version
     * @param Comparison $comparison
     * @return bool
     */
    private function isVersionFit(?string $version, Comparison $comparison): bool
    {
        return $version === null
            || $this->compareVersions((string)$this->getProductMetadata()->getVersion(), $version, $comparison);
    }

    /**
     * Check the declared Mage-OS version against the installed product.
     *
     * @param string|null $version
     * @param Comparison $comparison
     * @return bool
     */
    private function isDistributionVersionFit(?string $version, Comparison $comparison): bool
    {
        if ($version === null) {
            return true;
        }

        $productMetadata = $this->getProductMetadata();

        return method_exists($productMetadata, 'getDistributionVersion')
            && $this->compareVersions((string)$productMetadata->getDistributionVersion(), $version, $comparison);
    }

    /**
     * Compare an installed version with a declared one.
     *
     * @param string $installedVersion
     * @param string $declaredVersion
     * @param Comparison $comparison
     * @return bool
     */
    private function compareVersions(string $installedVersion, string $declaredVersion, Comparison $comparison): bool
    {
        return (bool)version_compare($installedVersion, $declaredVersion, $comparison->value);
    }

    /**
     * Check whether the installed product is Mage-OS.
     *
     * @return bool
     */
    private function isMageOs(): bool
    {
        $productMetadata = $this->getProductMetadata();

        return method_exists($productMetadata, 'getDistributionName')
            && $productMetadata->getDistributionName() === Distribution::MageOs->value;
    }

    /**
     * Get product metadata that does not use the application cache, which may still be under construction.
     *
     * @return ProductMetadataInterface
     */
    private function getProductMetadata(): ProductMetadataInterface
    {
        return $this->productMetadata ??= $this->productMetadataFactory->create(['cache' => $this->nullCache]);
    }
}
