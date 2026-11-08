<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Fixture;

use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;

#[Platform(edition: Edition::Community, version: '2.4.8')]
#[Platform(distribution: Distribution::MageOs, distributionVersion: '4.0.0')]
class WithPlatforms
{
}
