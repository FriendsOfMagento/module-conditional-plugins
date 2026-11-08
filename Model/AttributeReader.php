<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Model;

use Fom\ConditionalPlugins\Attribute\Operator;
use Fom\ConditionalPlugins\Attribute\Platform;
use Magento\Framework\Interception\ObjectManager\ConfigInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class AttributeReader
{
    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * Get the platform conditions declared on a class.
     *
     * @param string $className
     * @return Platform[]
     */
    public function getPlatformList(string $className): array
    {
        return $this->getAttributes($className, Platform::class);
    }

    /**
     * Get the operator declared on a class, or the default one.
     *
     * @param string $className
     * @return Operator
     */
    public function getOperator(string $className): Operator
    {
        return $this->getAttributes($className, Operator::class)[0] ?? new Operator();
    }

    /**
     * Instantiate the attributes of one type declared on a class.
     *
     * @template T of object
     * @param string $className
     * @param class-string<T> $attributeClass
     * @return T[]
     */
    private function getAttributes(string $className, string $attributeClass): array
    {
        try {
            $reflection = new ReflectionClass(
                $this->config->getOriginalInstanceType(ltrim($className, '\\'))
            );
        } catch (ReflectionException) {
            return [];
        }

        return array_map(
            static fn (ReflectionAttribute $attribute): object => $attribute->newInstance(),
            $reflection->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF)
        );
    }
}
