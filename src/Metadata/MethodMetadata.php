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
    public $reflection;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;

        $this->reflection = new \ReflectionMethod($class, $name);
        $this->reflection->setAccessible(true);
    }

    /**
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function invoke(object $obj, array $args = [])
    {
        return $this->reflection->invokeArgs($obj, $args);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([$this->class, $this->name]);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     *
     * @param string $str
     * @return void
     */
    public function unserialize($str)
    {
        list($this->class, $this->name) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
