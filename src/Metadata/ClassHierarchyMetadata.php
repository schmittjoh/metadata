<?php

declare(strict_types=1);

namespace Metadata;

/**
 * Represents the metadata for the entire class hierarchy.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassHierarchyMetadata
{
    /**
     * @var ClassMetadata[]
     */
    public $classMetadata = [];

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

    public function isFresh(int $timestamp): bool
    {
        foreach ($this->classMetadata as $metadata) {
            if (!$metadata->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }
}
