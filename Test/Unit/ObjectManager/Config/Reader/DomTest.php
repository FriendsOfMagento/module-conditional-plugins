<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\ObjectManager\Config\Reader;

use Fom\ConditionalPlugins\Model\UnsupportedEntriesDisabler;
use Fom\ConditionalPlugins\ObjectManager\Config\Reader\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\ObjectManager\Config\Mapper\Dom as Converter;
use Magento\Framework\ObjectManager\Config\SchemaLocator;
use PHPUnit\Framework\TestCase;

class DomTest extends TestCase
{
    /**
     * @return void
     */
    public function testDisablesPluginsWithoutTouchingOtherConfiguration(): void
    {
        $fileResolver = $this->createStub(FileResolverInterface::class);
        $fileResolver->method('get')->willReturn(['di.xml' => '<config/>']);

        $converter = $this->createStub(Converter::class);
        $converter->method('convert')->willReturn(
            [
                'preferences' => ['Vendor\Module\ModelInterface' => 'Vendor\Module\Model'],
                'Vendor\Module\Target' => [
                    'shared' => false,
                    'plugins' => ['example' => ['instance' => 'Vendor\Module\Plugin']],
                ],
                'Vendor\Module\Other' => ['shared' => true],
            ]
        );

        $unsupportedEntriesDisabler = $this->createMock(UnsupportedEntriesDisabler::class);
        $unsupportedEntriesDisabler->expects(self::once())
            ->method('execute')
            ->with(['example' => ['instance' => 'Vendor\Module\Plugin']])
            ->willReturn(['example' => ['instance' => 'Vendor\Module\Plugin', 'disabled' => true]]);

        $reader = new Dom(
            $fileResolver,
            $converter,
            $this->createStub(SchemaLocator::class),
            $this->createStub(ValidationStateInterface::class),
            $unsupportedEntriesDisabler
        );

        self::assertSame(
            [
                'preferences' => ['Vendor\Module\ModelInterface' => 'Vendor\Module\Model'],
                'Vendor\Module\Target' => [
                    'shared' => false,
                    'plugins' => ['example' => ['instance' => 'Vendor\Module\Plugin', 'disabled' => true]],
                ],
                'Vendor\Module\Other' => ['shared' => true],
            ],
            $reader->read('global')
        );
    }
}
