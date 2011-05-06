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

/**
 * Base class for class metadata.
 *
 * This class is intended to be extended to add your own application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassMetadata implements \Serializable
{
    private $name;
    private $reflection;
    private $methodMetadata = array();
    private $propertyMetadata = array();

    public function __construct($name)
    {
        $this->name = $name;

        $this->reflection = new \ReflectionClass($name);
    }

    public function addMethodMetadata(MethodMetadata $metadata)
    {
        $this->methodMetadata[$metadata->getName()] = $metadata;
    }

    public function addPropertyMetadata(PropertyMetadata $metadata)
    {
        $this->propertyMetadata[$metadata->getName()] = $metadata;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getMethodMetadata()
    {
        return $this->methodMetadata;
    }

    public function getPropertyMetadata()
    {
        return $this->propertyMetadata;
    }

    public function getLastModified()
    {
        if (false === $filename = $this->reflection->getFileName()) {
            return 0;
        }

        return filemtime($filename);
    }

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}