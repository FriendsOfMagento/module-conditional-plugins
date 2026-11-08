<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Test\Unit\Model;

use Fom\ConditionalPlugins\Attribute\Operator;
use Fom\ConditionalPlugins\Attribute\Platform;
use Fom\ConditionalPlugins\Enum\LogicalOperator;
use Fom\ConditionalPlugins\Model\AttributeReader;
use Fom\ConditionalPlugins\Model\ClassValidator;
use Fom\ConditionalPlugins\Model\PlatformValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ClassValidatorTest extends TestCase
{
    private const CLASS_NAME = 'Vendor\Module\Observer';

    /**
     * @dataProvider resultsProvider
     * @param LogicalOperator $operator
     * @param bool[] $results
     * @param bool $expected
     * @return void
     */
    #[DataProvider('resultsProvider')]
    public function testCombinesResults(LogicalOperator $operator, array $results, bool $expected): void
    {
        $platformList = array_map(
            static fn (int $index): Platform => new Platform(version: '2.4.' . $index),
            array_keys($results)
        );

        $attributeReader = $this->createStub(AttributeReader::class);
        $attributeReader->method('getPlatformList')->willReturn($platformList);
        $attributeReader->method('getOperator')->willReturn(new Operator($operator));

        $platformValidator = $this->createStub(PlatformValidator::class);
        $platformValidator->method('isFit')->willReturnOnConsecutiveCalls(...$results);

        $classValidator = new ClassValidator($attributeReader, $platformValidator);

        self::assertSame($expected, $classValidator->isExecutable(self::CLASS_NAME));
    }

    /**
     * @return array
     */
    public static function resultsProvider(): array
    {
        return [
            'and, all fit' => [LogicalOperator::And, [true, true], true],
            'and, one fits' => [LogicalOperator::And, [true, false], false],
            'and, none fit' => [LogicalOperator::And, [false, false], false],
            'or, all fit' => [LogicalOperator::Or, [true, true], true],
            'or, one fits' => [LogicalOperator::Or, [false, true], true],
            'or, none fit' => [LogicalOperator::Or, [false, false], false],
        ];
    }

    /**
     * @return void
     */
    public function testClassWithoutPlatformsIsExecutableForEveryOperator(): void
    {
        $attributeReader = $this->createStub(AttributeReader::class);
        $attributeReader->method('getPlatformList')->willReturn([]);
        $attributeReader->method('getOperator')->willReturn(new Operator(LogicalOperator::Or));

        $classValidator = new ClassValidator($attributeReader, $this->createStub(PlatformValidator::class));

        self::assertTrue($classValidator->isExecutable(self::CLASS_NAME));
    }
}
