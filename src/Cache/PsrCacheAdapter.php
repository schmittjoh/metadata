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
        $this->lastItem = $this->pool->getItem($this->sanitizeCacheKey($this->prefix . $class));

        return $this->lastItem->get();
    }

    /**
     * {@inheritDoc}
     */
    public function put(ClassMetadata $metadata): void
    {
        $key = $this->sanitizeCacheKey($this->prefix . $metadata->name);

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
        $this->pool->deleteItem($this->sanitizeCacheKey($this->prefix . $class));
    }

    /**
     * If anonymous class is to be cached, it contains invalid path characters that need to be removed/replaced
     * Example of anonymous class name: class@anonymous\x00/app/src/Controller/DefaultController.php0x7f82a7e026ec
     *
     * @param string $key
     * @return string
     */
    private function sanitizeCacheKey(string $key): string
    {
        return strtr($key, ['\\' => '.', "\0" => '', '@' => '.', '/' => '.']);
    }
}
