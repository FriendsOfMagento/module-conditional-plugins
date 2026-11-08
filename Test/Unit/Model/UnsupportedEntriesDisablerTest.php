<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Model;

use Fom\ConditionalPlugins\Model\ClassValidator;
use Fom\ConditionalPlugins\Model\UnsupportedEntriesDisabler;
use PHPUnit\Framework\TestCase;

class UnsupportedEntriesDisablerTest extends TestCase
{
    /**
     * @return void
     */
    public function testDisablesOnlyUnsupportedEntries(): void
    {
        $classValidator = $this->createStub(ClassValidator::class);
        $classValidator->method('isExecutable')->willReturnCallback(
            static fn (string $className): bool => $className === 'Supported'
        );

        self::assertSame(
            [
                'supported' => ['instance' => 'Supported'],
                'unsupported' => ['instance' => 'Unsupported', 'disabled' => true],
            ],
            (new UnsupportedEntriesDisabler($classValidator))->execute(
                [
                    'supported' => ['instance' => 'Supported'],
                    'unsupported' => ['instance' => 'Unsupported'],
                ]
            )
        );
    }

    /**
     * @return void
     */
    public function testSkipsEntriesThatNeedNoClassLoading(): void
    {
        $classValidator = $this->createMock(ClassValidator::class);
        $classValidator->expects(self::never())->method('isExecutable');

        $entries = [
            'without_instance' => ['sortOrder' => 10],
            'empty_instance' => ['instance' => ''],
            'already_disabled' => ['instance' => 'Vendor\Module\Missing', 'disabled' => true],
        ];

        self::assertSame($entries, (new UnsupportedEntriesDisabler($classValidator))->execute($entries));
    }
}
