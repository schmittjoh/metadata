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
 * Base class for method metadata.
 *
 * This class is intended to be extended to add your application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class MethodMetadata implements \Serializable
{
    public $class;
    public $name;
    public $reflection;

    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;

        $this->reflection = new \ReflectionMethod($class, $name);
        $this->reflection->setAccessible(true);
    }

    public function invoke($obj, array $args = array())
    {
        return $this->reflection->invokeArgs($obj, $args);
    }

    public function serialize()
    {
        return serialize(array($this->class, $this->name));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}