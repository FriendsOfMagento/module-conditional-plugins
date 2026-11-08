<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Fixture;

use Fom\ConditionalPlugins\Attribute\Operator;
use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Edition;
use Fom\ConditionalPlugins\Enum\LogicalOperator;

#[Platform(edition: Edition::Commerce)]
#[Operator(LogicalOperator::Or)]
class WithOperatorOr
{
}
