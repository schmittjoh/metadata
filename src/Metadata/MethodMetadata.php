<?php

namespace Metadata;

/**
 * Base class for method metadata.
 *
 * This class is intended to be extended to add your application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class MethodMetadata implements \Serializable
{
    public $class;
    public $name;
    public $reflection;

    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;

        $this->reflection = new \ReflectionMethod($class, $name);
        $this->reflection->setAccessible(true);
    }

    /**
     * @param object $obj
     * @param array  $args
     *
     * @return mixed
     */
    public function invoke($obj, array $args = array())
    {
        return $this->reflection->invokeArgs($obj, $args);
    }

    public function serialize()
    {
        return serialize(array($this->class, $this->name));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
