<?php

namespace Metadata\Driver;

interface FileLocatorInterface
{
    function findFileForClass(\ReflectionClass $class, $extension);
}