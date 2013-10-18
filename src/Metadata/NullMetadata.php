<?php

namespace Metadata;

/**
 * Represents the metadata for a class that has not metadata.
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class NullMetadata extends ClassMetadata
{
    public function __construct()
    {

    }

    public function serialize()
    {
        return '';
    }

    public function unserialize($str)
    {

    }
}
