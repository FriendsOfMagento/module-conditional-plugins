<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Fixture;

use Magento\Framework\App\ProductMetadataInterface;

class MageOsProductMetadata implements ProductMetadataInterface
{
    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return '2.4.9';
    }

    /**
     * @inheritDoc
     */
    public function getEdition()
    {
        return 'Community';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Magento';
    }

    /**
     * @return string
     */
    public function getDistributionName(): string
    {
        return 'Mage-OS';
    }

    /**
     * @return string
     */
    public function getDistributionVersion(): string
    {
        return '4.0.0';
    }
}
