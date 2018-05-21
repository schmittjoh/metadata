<?php

namespace Metadata\Driver;

interface DriverInterface
{
    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class);
}
