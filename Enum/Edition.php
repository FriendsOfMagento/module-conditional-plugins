<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Enum;

enum Edition: string
{
    case Any = 'any';
    case Community = 'Community';
    case Commerce = 'Enterprise';
    case B2b = 'B2B';
}
