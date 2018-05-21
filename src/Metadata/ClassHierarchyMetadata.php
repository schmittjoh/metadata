<?php

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
