<?php

declare(strict_types=1);

namespace Metadata\Driver;

interface FileLocatorInterface
{
    public function findFileForClass(\ReflectionClass $class, string $extension): ?string;
}
