<?php

declare(strict_types=1);

namespace Metadata\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ClearableCache;
use Metadata\ClassMetadata;

/**
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
class DoctrineCacheAdapter implements CacheInterface, ClearableCacheInterface
{
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(string $prefix, Cache $cache)
    {
        $this->prefix = $prefix;
        $this->cache = $cache;
    }

    public function load(string $class): ?ClassMetadata
    {
        $cache = $this->cache->fetch($this->prefix . $class);

        return false === $cache ? null : $cache;
    }

    public function put(ClassMetadata $metadata): void
    {
        $this->cache->save($this->prefix . $metadata->name, $metadata);
    }

    public function evict(string $class): void
    {
        $this->cache->delete($this->prefix . $class);
    }

    public function clear(): bool
    {
        if ($this->cache instanceof ClearableCache) {
            return $this->cache->deleteAll();
        }

        return false;
    }
}
