<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Enum;

enum LogicalOperator: string
{
    case And = 'and';
    case Or = 'or';
}
