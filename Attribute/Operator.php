<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Attribute;

use Attribute;
use Fom\ConditionalPlugins\Enum\LogicalOperator;

#[Attribute(Attribute::TARGET_CLASS)]
class Operator
{
    /**
     * @param LogicalOperator $operator
     */
    public function __construct(
        public readonly LogicalOperator $operator = LogicalOperator::And
    ) {
    }
}
