<?php

declare(strict_types=1);

namespace Fom\ConditionalPlugins\Model;

use LogicException;
use Magento\Framework\App\CacheInterface;

class NullCache implements CacheInterface
{
    /**
     * @inheritDoc
     *
     * @throws LogicException
     */
    public function getFrontend()
    {
        throw new LogicException('NullCache has no cache frontend.');
    }

    /**
     * @inheritDoc
     */
    public function load($identifier)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function save($data, $identifier, $tags = [], $lifeTime = null)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove($identifier)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function clean($tags = [])
    {
        return true;
    }
}
