<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Model;

use Fom\ConditionalPlugins\Model\NullCache;
use LogicException;
use PHPUnit\Framework\TestCase;

class NullCacheTest extends TestCase
{
    /**
     * @return void
     */
    public function testNothingIsStored(): void
    {
        $cache = new NullCache();

        self::assertTrue($cache->save('value', 'identifier'));
        self::assertSame('', $cache->load('identifier'));
        self::assertTrue($cache->remove('identifier'));
        self::assertTrue($cache->clean());
    }

    /**
     * @return void
     */
    public function testHasNoFrontend(): void
    {
        $this->expectException(LogicException::class);

        (new NullCache())->getFrontend();
    }
}
