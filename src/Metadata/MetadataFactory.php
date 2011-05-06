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

final class MetadataFactory implements MetadataFactoryInterface
{
    private $driver;
    private $loadedMetadata = array();
    private $loadedClassMetadata = array();
    private $hierarchyMetadataClass;

    public function __construct(DriverInterface $driver, $hierarchyMetadataClass = 'Metadata\ClassHierarchyMetadata')
    {
        $this->driver = $driver;
        $this->hierarchyMetadataClass = $hierarchyMetadataClass;
    }

    public function getMetadataForClass($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $metadata = new $this->hierarchyMetadataClass;
        foreach ($this->getClassHierarchy($className) as $class) {
            if (!isset($this->loadedClassMetadata[$name = $class->getName()])) {
                if (null === $classMetadata = $this->driver->loadMetadataForClass($class)) {
                    continue;
                }

                $this->loadedClassMetadata[$name] = $classMetadata;
            }

            $metadata->addClassMetadata($this->loadedClassMetadata[$name]);
        }

        if (!$metadata->classMetadata) {
            throw new \RuntimeException(sprintf('There is no metadata for class "%s".', $className));
        }

        return $this->loadedMetadata[$className] = $metadata;
    }

    private function getClassHierarchy($class)
    {
        $classes = array();
        $refl = new \ReflectionClass($class);

        do {
            $classes[] = $refl;
        } while (false !== $refl = $refl->getParentClass());

        return array_reverse($classes, false);
    }
}