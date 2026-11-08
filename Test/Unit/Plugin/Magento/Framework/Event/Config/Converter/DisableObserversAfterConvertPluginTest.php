<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Plugin\Magento\Framework\Event\Config\Converter;

use Fom\ConditionalPlugins\Model\ClassValidator;
use Fom\ConditionalPlugins\Model\UnsupportedEntriesDisabler;
use Fom\ConditionalPlugins\Plugin\Magento\Framework\Event\Config\Converter\DisableObserversAfterConvertPlugin;
use Magento\Framework\Event\Config\Converter;
use PHPUnit\Framework\TestCase;

class DisableObserversAfterConvertPluginTest extends TestCase
{
    /**
     * @return void
     */
    public function testDisablesUnsupportedObserversOfEveryEvent(): void
    {
        $classValidator = $this->createStub(ClassValidator::class);
        $classValidator->method('isExecutable')->willReturnCallback(
            static fn (string $className): bool => $className === 'Supported'
        );

        $plugin = new DisableObserversAfterConvertPlugin(new UnsupportedEntriesDisabler($classValidator));

        self::assertSame(
            [
                'first_event' => [
                    'supported' => ['instance' => 'Supported', 'name' => 'supported'],
                ],
                'second_event' => [
                    'unsupported' => ['instance' => 'Unsupported', 'name' => 'unsupported', 'disabled' => true],
                ],
            ],
            $plugin->afterConvert(
                $this->createStub(Converter::class),
                [
                    'first_event' => [
                        'supported' => ['instance' => 'Supported', 'name' => 'supported'],
                    ],
                    'second_event' => [
                        'unsupported' => ['instance' => 'Unsupported', 'name' => 'unsupported'],
                    ],
                ]
            )
        );
    }
}
