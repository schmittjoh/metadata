<?php

declare(strict_types=1);

namespace Metadata\Driver;

interface TraceableFileLocatorInterface extends FileLocatorInterface
{
    /**
     * Finds all possible metadata files for a class
     *
     * @return string[]
     */
    public function getPossibleFilesForClass(\ReflectionClass $class, string $extension): array;
}
