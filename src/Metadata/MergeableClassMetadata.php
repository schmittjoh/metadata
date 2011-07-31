<?php

namespace Metadata;

class MergeableClassMetadata extends ClassMetadata implements MergeableInterface
{
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof MergeableClassMetadata) {
            throw new \InvalidArgumentException('$object must be an instance of MergeableClassMetadata.');
        }

        $this->name = $object->name;
        $this->reflection = $object->reflection;
        $this->methodMetadata = array_merge($this->methodMetadata, $object->methodMetadata);
        $this->propertyMetadata = array_merge($this->propertyMetadata, $object->propertyMetadata);
        $this->fileResources = array_merge($this->fileResources, $object->fileResources);

        if ($object->createdAt < $this->createdAt) {
            $this->createdAt = $object->createdAt;
        }
    }
}