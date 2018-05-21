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

    public function addClassMetadata(ClassMetadata $metadata): void
    {
        $this->classMetadata[$metadata->name] = $metadata;
    }

    public function getRootClassMetadata(): ?ClassMetadata
    {
        return reset($this->classMetadata);
    }

    public function getOutsideClassMetadata(): ?ClassMetadata
    {
        return end($this->classMetadata);
    }

    public function isFresh($timestamp): bool
    {
        foreach ($this->classMetadata as $metadata) {
            if (!$metadata->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }
}
