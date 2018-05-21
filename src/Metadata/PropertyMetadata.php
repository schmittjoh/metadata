<?php

namespace Metadata;

/**
 * Base class for property metadata.
 *
 * This class is intended to be extended to add your application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PropertyMetadata implements \Serializable
{
    public $class;
    public $name;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
        ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name) = unserialize($str);
    }
}
