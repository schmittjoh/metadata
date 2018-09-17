<?php

declare(strict_types=1);

namespace Metadata\Driver;

use Metadata\ClassMetadata;

interface DriverInterface
{
    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata;
}
