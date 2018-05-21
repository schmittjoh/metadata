<?php

namespace Metadata;

/**
 * Interface for Metadata Factory implementations.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface MetadataFactoryInterface
{
    /**
     * Returns the gathered metadata for the given class name.
     *
     * If the drivers return instances of MergeableClassMetadata, these will be
     * merged prior to returning. Otherwise, all metadata for the inheritance
     * hierarchy will be returned as ClassHierarchyMetadata unmerged.
     *
     * If no metadata is available, null is returned.
     *
     * @param string $className
     *
     * @return ClassHierarchyMetadata|MergeableClassMetadata|null
     */
    public function getMetadataForClass($className);
}
