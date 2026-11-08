<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Enum;

enum Distribution: string
{
    case Any = 'any';
    case Magento = 'Magento';
    case MageOs = 'Mage-OS';
}
