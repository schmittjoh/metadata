<?php

namespace Metadata\Cache;

use Metadata\ClassMetadata;

interface CacheInterface
{
    /**
     * Loads a class metadata instance from the cache
     *
     * @param \ReflectionClass $class
     *
     * @return ClassMetadata
     */
    function loadClassMetadataFromCache(\ReflectionClass $class);

    /**
     * Puts a class metadata instance into the cache
     *
     * @param ClassMetadata $metadata
     *
     * @return void
     */
    function putClassMetadataInCache(ClassMetadata $metadata);

    /**
     * Evicts the class metadata for the given class from the cache.
     *
     * @param \ReflectionClass $class
     *
     * @return void
     */
    function evictClassMetadataFromCache(\ReflectionClass $class);
}