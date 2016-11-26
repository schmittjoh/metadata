<?php

namespace Metadata\Cache;

use Metadata\ClassMetadata;
use Psr\Cache\CacheItemPoolInterface;

class PsrCacheAdapter implements CacheInterface
{
    private $prefix;
    private $pool;
    private $lastItem;

    public function __construct($prefix, CacheItemPoolInterface $pool)
    {
        $this->prefix = $prefix;
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        $this->lastItem = $this->pool->getItem(strtr($this->prefix . $class->name, '\\', '.'));

        return $this->lastItem->get();
    }

    /**
     * {@inheritDoc}
     */
    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $key = strtr($this->prefix . $metadata->name, '\\', '.');

        if (null === $this->lastItem || $this->lastItem->getKey() !== $key) {
            $this->lastItem = $this->pool->getItem($key);
        }

        $this->pool->save($this->lastItem->set($metadata));
    }

    /**
     * {@inheritDoc}
     */
    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $this->pool->deleteItem(strtr($this->prefix . $class->name, '\\', '.'));
    }
}
