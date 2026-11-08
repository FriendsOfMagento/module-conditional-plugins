<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\ObjectManager\Config\Reader;

use Fom\ConditionalPlugins\Model\UnsupportedEntriesDisabler;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\ObjectManager\Config\Mapper\Dom as Converter;
use Magento\Framework\ObjectManager\Config\Reader\Dom as Reader;
use Magento\Framework\ObjectManager\Config\SchemaLocator;

class Dom extends Reader
{
    private const KEY_PLUGINS = 'plugins';

    /**
     * @param FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param UnsupportedEntriesDisabler $unsupportedEntriesDisabler
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        private readonly UnsupportedEntriesDisabler $unsupportedEntriesDisabler,
        $fileName = 'di.xml',
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Read the configuration and disable plugins that do not fit the current platform.
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null): array
    {
        $result = parent::read($scope);
        foreach ($result as $type => $config) {
            if (isset($config[self::KEY_PLUGINS])) {
                $result[$type][self::KEY_PLUGINS] = $this->unsupportedEntriesDisabler->execute(
                    $config[self::KEY_PLUGINS]
                );
            }
        }

        return $result;
    }
}
