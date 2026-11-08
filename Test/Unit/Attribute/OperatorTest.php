<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Attribute;

use Fom\ConditionalPlugins\Attribute\Operator;
use Fom\ConditionalPlugins\Enum\LogicalOperator;
use PHPUnit\Framework\TestCase;

class OperatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testDefaultsToAnd(): void
    {
        self::assertSame(LogicalOperator::And, (new Operator())->operator);
    }

    /**
     * @return void
     */
    public function testKeepsDeclaredOperator(): void
    {
        self::assertSame(LogicalOperator::Or, (new Operator(LogicalOperator::Or))->operator);
    }
}
