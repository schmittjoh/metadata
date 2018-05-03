<?php

namespace Metadata\Cache;

use Metadata\ClassMetadata;

interface CacheInterface
{
    /**
     * Loads a class metadata instance from the cache
     *
     * @param string $class
     *
     * @return ClassMetadata
     */
    function load(string $class): ?ClassMetadata;

    /**
     * Puts a class metadata instance into the cache
     *
     * @param ClassMetadata $metadata
     *
     * @return void
     */
    function put(ClassMetadata $metadata):void;

    /**
     * Evicts the class metadata for the given class from the cache.
     *
     * @param string $class
     *
     * @return void
     */
    function evict(string $class):void;
}
