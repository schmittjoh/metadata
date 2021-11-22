<?php

declare(strict_types=1);

namespace Metadata;

/**
 * Base class for method metadata.
 *
 * This class is intended to be extended to add your application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @property $reflection
 */
class MethodMetadata implements \Serializable
{
    use SerializationHelper;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \ReflectionMethod
     */
    private $reflection;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    /**
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function invoke(object $obj, array $args = [])
    {
        return $this->getReflection()->invokeArgs($obj, $args);
    }

    /**
     * @return mixed
     */
    public function __get(string $propertyName)
    {
        if ('reflection' === $propertyName) {
            return $this->getReflection();
        }

        return $this->$propertyName;
    }

    /**
     * @param mixed $value
     */
    public function __set(string $propertyName, $value): void
    {
        $this->$propertyName = $value;
    }

    private function getReflection(): \ReflectionMethod
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionMethod($this->class, $this->name);
            $this->reflection->setAccessible(true);
        }

        return $this->reflection;
    }

    protected function serializeToArray(): array
    {
        return [$this->class, $this->name];
    }

    protected function unserializeFromArray(array $data): void
    {
        [$this->class, $this->name] = $data;
    }
}
