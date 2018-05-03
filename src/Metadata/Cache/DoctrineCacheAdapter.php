<?php

namespace Metadata\Cache;

use Doctrine\Common\Cache\Cache;
use Metadata\ClassMetadata;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class DoctrineCacheAdapter implements CacheInterface
{
    /**
     * @param string $prefix
     */
    private $prefix;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @param string $prefix
     * @param Cache $cache
     */
    public function __construct($prefix, Cache $cache)
    {
        $this->prefix = $prefix;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $class):?ClassMetadata
    {
        $cache = $this->cache->fetch($this->prefix . $class);
        return false === $cache ? null : $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function put(ClassMetadata $metadata):void
    {
        $this->cache->save($this->prefix . $metadata->name, $metadata);
    }

    /**
     * {@inheritDoc}
     */
    public function evict(string $class):void
    {
        $this->cache->delete($this->prefix . $class);
    }
}
