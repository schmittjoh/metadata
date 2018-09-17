<?php

declare(strict_types=1);

namespace Metadata\Cache;

use Metadata\ClassMetadata;
use Psr\Cache\CacheItemPoolInterface;

class PsrCacheAdapter implements CacheInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    /**
     * @var CacheItemPoolInterface
     */
    private $lastItem;

    public function __construct(string $prefix, CacheItemPoolInterface $pool)
    {
        $this->prefix = $prefix;
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $class): ?ClassMetadata
    {
        $this->lastItem = $this->pool->getItem(strtr($this->prefix . $class, '\\', '.'));

        return $this->lastItem->get();
    }

    /**
     * {@inheritDoc}
     */
    public function put(ClassMetadata $metadata): void
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
    public function evict(string $class): void
    {
        $this->pool->deleteItem(strtr($this->prefix . $class, '\\', '.'));
    }
}
