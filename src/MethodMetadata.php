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
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function serialize()
    {
        return serialize([$this->class, $this->name]);
    }

    /**
     * @param string $str
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function unserialize($str)
    {
        [$this->class, $this->name] = unserialize($str);
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
}
