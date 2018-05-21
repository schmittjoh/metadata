<?php

namespace Metadata\Driver;

use Metadata\ClassMetadata;

interface DriverInterface
{
    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata;
}
