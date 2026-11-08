<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Plugin\Magento\Framework\Event\Config\Converter;

use Fom\ConditionalPlugins\Model\UnsupportedEntriesDisabler;
use Magento\Framework\Event\Config\Converter;

class DisableObserversAfterConvertPlugin
{
    /**
     * @param UnsupportedEntriesDisabler $unsupportedEntriesDisabler
     */
    public function __construct(
        private readonly UnsupportedEntriesDisabler $unsupportedEntriesDisabler
    ) {
    }

    /**
     * Disable observers that do not fit the current platform.
     *
     * @param Converter $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(Converter $subject, array $result): array
    {
        return array_map($this->unsupportedEntriesDisabler->execute(...), $result);
    }
}
