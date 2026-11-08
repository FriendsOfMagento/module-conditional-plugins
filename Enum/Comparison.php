<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Enum;

enum Comparison: string
{
    case LessThan = '<';
    case LessThanOrEqual = '<=';
    case GreaterThan = '>';
    case GreaterThanOrEqual = '>=';
    case Equal = '=';
    case NotEqual = '!=';
}
