<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Model;

class UnsupportedEntriesDisabler
{
    private const KEY_INSTANCE = 'instance';
    private const KEY_DISABLED = 'disabled';

    /**
     * @param ClassValidator $classValidator
     */
    public function __construct(
        private readonly ClassValidator $classValidator
    ) {
    }

    /**
     * Mark plugin or observer entries as disabled when their class does not fit the current platform.
     *
     * @param array $entries
     * @return array
     */
    public function execute(array $entries): array
    {
        foreach ($entries as $name => $entry) {
            $instance = (string)($entry[self::KEY_INSTANCE] ?? '');
            if ($instance === '' || !empty($entry[self::KEY_DISABLED])) {
                continue;
            }

            if (!$this->classValidator->isExecutable($instance)) {
                $entries[$name][self::KEY_DISABLED] = true;
            }
        }

        return $entries;
    }
}
