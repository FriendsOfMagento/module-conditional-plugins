<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Model;

use Fom\ConditionalPlugins\Enum\LogicalOperator;

class ClassValidator
{
    /**
     * @param AttributeReader $attributeReader
     * @param PlatformValidator $platformValidator
     */
    public function __construct(
        private readonly AttributeReader $attributeReader,
        private readonly PlatformValidator $platformValidator
    ) {
    }

    /**
     * Check whether a class may run on the current platform.
     *
     * @param string $className
     * @return bool
     */
    public function isExecutable(string $className): bool
    {
        $platformList = $this->attributeReader->getPlatformList($className);
        if ($platformList === []) {
            return true;
        }

        $results = array_map($this->platformValidator->isFit(...), $platformList);

        return match ($this->attributeReader->getOperator($className)->operator) {
            LogicalOperator::Or => in_array(true, $results, true),
            LogicalOperator::And => !in_array(false, $results, true),
        };
    }
}
