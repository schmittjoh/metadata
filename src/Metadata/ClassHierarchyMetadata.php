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
 * Represents the metadata for the entire class hierarchy.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassHierarchyMetadata
{
    public $classMetadata = array();

    public function addClassMetadata(ClassMetadata $metadata)
    {
        $this->classMetadata[$metadata->name] = $metadata;
    }

    public function getRootClassMetadata()
    {
        return reset($this->classMetadata);
    }

    public function getOutsideClassMetadata()
    {
        return end($this->classMetadata);
    }

    public function isFresh($timestamp)
    {
        foreach ($this->classMetadata as $metadata) {
            if (!$metadata->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }
}