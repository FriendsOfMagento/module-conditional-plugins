<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Attribute;

use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Comparison;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;
use PHPUnit\Framework\TestCase;

class PlatformTest extends TestCase
{
    /**
     * @return void
     */
    public function testDefaults(): void
    {
        $platform = new Platform();

        self::assertSame(Edition::Any, $platform->edition);
        self::assertNull($platform->version);
        self::assertSame(Comparison::GreaterThanOrEqual, $platform->comparison);
        self::assertSame(Distribution::Any, $platform->distribution);
        self::assertNull($platform->distributionVersion);
    }

    /**
     * @return void
     */
    public function testKeepsDeclaredValues(): void
    {
        $platform = new Platform(Edition::Community, '2.4.9', Comparison::Equal, Distribution::MageOs, '4.0.0');

        self::assertSame(Edition::Community, $platform->edition);
        self::assertSame('2.4.9', $platform->version);
        self::assertSame(Comparison::Equal, $platform->comparison);
        self::assertSame(Distribution::MageOs, $platform->distribution);
        self::assertSame('4.0.0', $platform->distributionVersion);
    }
}
