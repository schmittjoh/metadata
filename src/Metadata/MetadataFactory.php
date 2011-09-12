<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Metadata;

use Metadata\Driver\DriverInterface;
use Metadata\Cache\CacheInterface;

final class MetadataFactory implements MetadataFactoryInterface
{
    private $driver;
    private $cache;
    private $loadedMetadata = array();
    private $loadedClassMetadata = array();
    private $hierarchyMetadataClass;
    private $includeInterfaces = false;
    private $debug;

    public function __construct(DriverInterface $driver, $hierarchyMetadataClass = 'Metadata\ClassHierarchyMetadata', $debug = false)
    {
        $this->driver = $driver;
        $this->hierarchyMetadataClass = $hierarchyMetadataClass;
        $this->debug = $debug;
    }

    public function setIncludeInterfaces($bool)
    {
        $this->includeInterfaces = (Boolean) $bool;
    }

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getMetadataForClass($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $metadata = null;
        foreach ($this->getClassHierarchy($className) as $class) {
            if (isset($this->loadedClassMetadata[$name = $class->getName()])) {
                $this->addClassMetadata($metadata, $this->loadedClassMetadata[$name]);
                continue;
            }

            // check the cache
            if (null !== $this->cache
                && (null !== $classMetadata = $this->cache->loadClassMetadataFromCache($class))) {
                if ($this->debug && !$classMetadata->isFresh()) {
                    $this->cache->evictClassMetadataFromCache($classMetadata->reflection);
                } else {
                    $this->loadedClassMetadata[$name] = $classMetadata;
                    $this->addClassMetadata($metadata, $classMetadata);
                    continue;
                }
            }

            // load from source
            if (null !== $classMetadata = $this->driver->loadMetadataForClass($class)) {
                $this->loadedClassMetadata[$name] = $classMetadata;
                $this->addClassMetadata($metadata, $classMetadata);

                if (null !== $this->cache) {
                    $this->cache->putClassMetadataInCache($classMetadata);
                }

                continue;
            }
        }

        return $this->loadedMetadata[$className] = $metadata;
    }

    private function addClassMetadata(&$metadata, $toAdd)
    {
        if ($toAdd instanceof MergeableInterface) {
            if (null === $metadata) {
                $metadata = clone $toAdd;
            } else {
                $metadata->merge($toAdd);
            }
        } else {
            if (null === $metadata) {
                $metadata = new $this->hierarchyMetadataClass;
            }

            $metadata->addClassMetadata($toAdd);
        }
    }

    private function getClassHierarchy($class)
    {
        $classes = array();
        $refl = new \ReflectionClass($class);

        do {
            $classes[] = $refl;
        } while (false !== $refl = $refl->getParentClass());

        $classes = array_reverse($classes, false);

        if (!$this->includeInterfaces) {
            return $classes;
        }

        $addedInterfaces = array();
        $newHierarchy = array();

        foreach ($classes as $class) {
            foreach ($class->getInterfaces() as $interface) {
                if (isset($addedInterfaces[$interface->getName()])) {
                    continue;
                }
                $addedInterfaces[$interface->getName()] = true;

                $newHierarchy[] = $interface;
            }

            $newHierarchy[] = $class;
        }

        return $newHierarchy;
    }
}