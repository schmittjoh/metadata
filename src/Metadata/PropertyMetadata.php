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
    public $reflection;

    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;

        $this->reflection = new \ReflectionProperty($class, $name);
        $this->reflection->setAccessible(true);
    }

    /**
     * @param object $obj
     *
     * @return mixed
     */
    public function getValue($obj)
    {
        return $this->reflection->getValue($obj);
    }

    /**
     * @param object $obj
     * @param string $value
     */
    public function setValue($obj, $value)
    {
        $this->reflection->setValue($obj, $value);
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

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
