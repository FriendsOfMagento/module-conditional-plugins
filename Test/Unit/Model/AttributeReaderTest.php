<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Model;

use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;
use Fom\ConditionalPlugins\Enum\LogicalOperator;
use Fom\ConditionalPlugins\Model\AttributeReader;
use Fom\ConditionalPlugins\Test\Unit\Fixture\WithOperatorOr;
use Fom\ConditionalPlugins\Test\Unit\Fixture\WithoutAttributes;
use Fom\ConditionalPlugins\Test\Unit\Fixture\WithPlatforms;
use Magento\Framework\Interception\ObjectManager\ConfigInterface;
use PHPUnit\Framework\TestCase;

class AttributeReaderTest extends TestCase
{
    /**
     * @var AttributeReader
     */
    private AttributeReader $attributeReader;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $config = $this->createStub(ConfigInterface::class);
        $config->method('getOriginalInstanceType')->willReturnCallback(
            static fn (string $className): string => $className === 'virtualObserver'
                ? WithPlatforms::class
                : $className
        );
        $config->method('getInstanceType')->willReturnCallback(
            static fn (string $className): string => $className . '\\Interceptor'
        );

        $this->attributeReader = new AttributeReader($config);
    }

    /**
     * @return void
     */
    public function testReadsRepeatedPlatforms(): void
    {
        $platformList = $this->attributeReader->getPlatformList(WithPlatforms::class);

        self::assertCount(2, $platformList);
        self::assertContainsOnlyInstancesOf(Platform::class, $platformList);
        self::assertSame(Edition::Community, $platformList[0]->edition);
        self::assertSame(Distribution::MageOs, $platformList[1]->distribution);
    }

    /**
     * @return void
     */
    public function testAcceptsLeadingBackslash(): void
    {
        self::assertCount(2, $this->attributeReader->getPlatformList('\\' . WithPlatforms::class));
    }

    /**
     * @return void
     */
    public function testResolvesVirtualTypes(): void
    {
        self::assertCount(2, $this->attributeReader->getPlatformList('virtualObserver'));
    }

    /**
     * @return void
     */
    public function testClassWithoutAttributesHasNoPlatforms(): void
    {
        self::assertSame([], $this->attributeReader->getPlatformList(WithoutAttributes::class));
    }

    /**
     * @return void
     */
    public function testMissingClassHasNoPlatforms(): void
    {
        self::assertSame([], $this->attributeReader->getPlatformList('Vendor\Module\Missing'));
    }

    /**
     * @return void
     */
    public function testReadsDeclaredOperator(): void
    {
        self::assertSame(LogicalOperator::Or, $this->attributeReader->getOperator(WithOperatorOr::class)->operator);
    }

    /**
     * @return void
     */
    public function testOperatorDefaultsToAnd(): void
    {
        self::assertSame(LogicalOperator::And, $this->attributeReader->getOperator(WithoutAttributes::class)->operator);
    }
}
