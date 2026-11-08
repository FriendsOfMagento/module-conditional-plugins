<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Attribute;

use Attribute;
use Fom\ConditionalPlugins\Enum\Comparison;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Platform
{
    /**
     * @param Edition $edition
     * @param string|null $version
     * @param Comparison $comparison
     * @param Distribution $distribution
     * @param string|null $distributionVersion
     */
    public function __construct(
        public readonly Edition $edition = Edition::Any,
        public readonly ?string $version = null,
        public readonly Comparison $comparison = Comparison::GreaterThanOrEqual,
        public readonly Distribution $distribution = Distribution::Any,
        public readonly ?string $distributionVersion = null
    ) {
    }
}
