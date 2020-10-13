<?php

declare(strict_types=1);

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
     * @return ClassHierarchyMetadata|MergeableClassMetadata|null
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function getMetadataForClass(string $className);
}
