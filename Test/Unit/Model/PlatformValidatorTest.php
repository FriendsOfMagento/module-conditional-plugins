<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Model;

use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\Comparison;
use Fom\ConditionalPlugins\Enum\Distribution;
use Fom\ConditionalPlugins\Enum\Edition;
use Fom\ConditionalPlugins\Model\NullCache;
use Fom\ConditionalPlugins\Model\PlatformValidator;
use Fom\ConditionalPlugins\Test\Unit\Fixture\MageOsProductMetadata;
use InvalidArgumentException;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ProductMetadataInterfaceFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PlatformValidatorTest extends TestCase
{
    /**
     * @dataProvider magentoProvider
     * @param string $edition
     * @param string $version
     * @param Platform $platform
     * @param bool $expected
     * @return void
     */
    #[DataProvider('magentoProvider')]
    public function testMagento(string $edition, string $version, Platform $platform, bool $expected): void
    {
        $productMetadata = $this->createStub(ProductMetadataInterface::class);
        $productMetadata->method('getEdition')->willReturn($edition);
        $productMetadata->method('getVersion')->willReturn($version);

        self::assertSame($expected, $this->createValidator($productMetadata)->isFit($platform));
    }

    /**
     * @return array
     */
    public static function magentoProvider(): array
    {
        $community = Edition::Community->value;
        $commerce = Edition::Commerce->value;
        $b2b = Edition::B2b->value;

        return [
            'any platform' => [$community, '2.4.8', new Platform(), true],
            'same edition' => [$community, '2.4.8', new Platform(Edition::Community), true],
            'other edition' => [$community, '2.4.8', new Platform(Edition::Commerce), false],
            'commerce' => [$commerce, '2.4.8', new Platform(Edition::Commerce), true],
            'b2b' => [$b2b, '2.4.8', new Platform(Edition::B2b), true],
            'b2b is not commerce' => [$b2b, '2.4.8', new Platform(Edition::Commerce), false],
            'magento distribution' => [$community, '2.4.8', new Platform(distribution: Distribution::Magento), true],
            'commerce is magento distribution' => [
                $commerce, '2.4.8', new Platform(distribution: Distribution::Magento), true,
            ],
            'not mage-os' => [$community, '2.4.8', new Platform(distribution: Distribution::MageOs), false],
            'not mage-os version' => [
                $community,
                '2.4.8',
                new Platform(distribution: Distribution::MageOs, distributionVersion: '1.0.0'),
                false,
            ],
            'version reached' => [$community, '2.4.8', new Platform(version: '2.4.8'), true],
            'version not reached' => [$community, '2.4.7', new Platform(version: '2.4.8'), false],
            'less than' => [
                $community, '2.4.7', new Platform(version: '2.4.8', comparison: Comparison::LessThan), true,
            ],
            'less or equal' => [
                $community, '2.4.8', new Platform(version: '2.4.8', comparison: Comparison::LessThanOrEqual), true,
            ],
            'greater than' => [
                $community, '2.4.8', new Platform(version: '2.4.8', comparison: Comparison::GreaterThan), false,
            ],
            'equal' => [$community, '2.4.8', new Platform(version: '2.4.8', comparison: Comparison::Equal), true],
            'not equal' => [
                $community, '2.4.8', new Platform(version: '2.4.8', comparison: Comparison::NotEqual), false,
            ],
            'patch above release' => [$community, '2.4.7-p10', new Platform(version: '2.4.7'), true],
            'patch numbers compare' => [$community, '2.4.7-p10', new Platform(version: '2.4.7-p9'), true],
            'next release above patch' => [$community, '2.4.7-p10', new Platform(version: '2.4.8'), false],
            'edition and version must both fit' => [
                $community, '2.4.8', new Platform(Edition::Commerce, '2.4.0'), false,
            ],
        ];
    }

    /**
     * @dataProvider mageOsProvider
     * @param Platform $platform
     * @param bool $expected
     * @return void
     */
    #[DataProvider('mageOsProvider')]
    public function testMageOs(Platform $platform, bool $expected): void
    {
        self::assertSame($expected, $this->createValidator(new MageOsProductMetadata())->isFit($platform));
    }

    /**
     * @return array
     */
    public static function mageOsProvider(): array
    {
        $mageOs = Distribution::MageOs;

        return [
            'mage-os' => [new Platform(distribution: $mageOs), true],
            'not magento distribution' => [new Platform(distribution: Distribution::Magento), false],
            'mage-os version reached' => [new Platform(distribution: $mageOs, distributionVersion: '4.0.0'), true],
            'mage-os version not reached' => [new Platform(distribution: $mageOs, distributionVersion: '4.1.0'), false],
            'mage-os and magento version together' => [
                new Platform(Edition::Community, '2.4.9', Comparison::Equal, $mageOs, '4.0.0'),
                true,
            ],
            'magento version differs from mage-os version' => [
                new Platform(Edition::Community, '4.0.0', Comparison::Equal, $mageOs),
                false,
            ],
            'community' => [new Platform(Edition::Community), true],
            'community uses the magento version' => [
                new Platform(Edition::Community, '2.4.9', Comparison::Equal),
                true,
            ],
            'any edition uses the magento version' => [
                new Platform(version: '2.4.9', comparison: Comparison::Equal),
                true,
            ],
            'commerce' => [new Platform(Edition::Commerce), false],
        ];
    }

    /**
     * @dataProvider invalidProvider
     * @param Platform $platform
     * @param string $message
     * @return void
     */
    #[DataProvider('invalidProvider')]
    public function testInvalidConditionIsRejected(Platform $platform, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->createValidator(new MageOsProductMetadata())->isFit($platform);
    }

    /**
     * @return array
     */
    public static function invalidProvider(): array
    {
        return [
            'empty version' => [new Platform(version: ''), 'must not be empty'],
            'empty distribution version' => [
                new Platform(distribution: Distribution::MageOs, distributionVersion: ''),
                'must not be empty',
            ],
            'distribution version without mage-os' => [
                new Platform(distributionVersion: '4.0.0'),
                'requires Distribution::MageOs',
            ],
            'distribution version on magento' => [
                new Platform(distribution: Distribution::Magento, distributionVersion: '4.0.0'),
                'requires Distribution::MageOs',
            ],
        ];
    }

    /**
     * @return void
     */
    public function testProductMetadataBypassesTheApplicationCache(): void
    {
        $nullCache = new NullCache();
        $factory = $this->createMock(ProductMetadataInterfaceFactory::class);
        $factory->expects(self::once())
            ->method('create')
            ->with(['cache' => $nullCache])
            ->willReturn(new MageOsProductMetadata());

        $validator = new PlatformValidator($factory, $nullCache);

        self::assertTrue($validator->isFit(new Platform(distribution: Distribution::MageOs)));
        self::assertTrue($validator->isFit(new Platform(version: '2.4.9')));
    }

    /**
     * @param ProductMetadataInterface $productMetadata
     * @return PlatformValidator
     */
    private function createValidator(ProductMetadataInterface $productMetadata): PlatformValidator
    {
        $factory = $this->createStub(ProductMetadataInterfaceFactory::class);
        $factory->method('create')->willReturn($productMetadata);

        return new PlatformValidator($factory, new NullCache());
    }
}
