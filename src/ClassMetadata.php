<?php

declare(strict_types=1);

namespace Metadata;

/**
 * Base class for class metadata.
 *
 * This class is intended to be extended to add your own application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassMetadata implements \Serializable
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string
     */
    public $name;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var MethodMetadata[]
     */
    public $methodMetadata = [];

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var PropertyMetadata[]
     */
    public $propertyMetadata = [];

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string[]
     */
    public $fileResources = [];

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var int
     */
    public $createdAt;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->createdAt = time();
    }

    public function addMethodMetadata(MethodMetadata $metadata): void
    {
        $this->methodMetadata[$metadata->name] = $metadata;
    }

    public function addPropertyMetadata(PropertyMetadata $metadata): void
    {
        $this->propertyMetadata[$metadata->name] = $metadata;
    }

    public function isFresh(?int $timestamp = null): bool
    {
        if (null === $timestamp) {
            $timestamp = $this->createdAt;
        }

        foreach ($this->fileResources as $filepath) {
            if (!file_exists($filepath)) {
                return false;
            }

            if ($timestamp < filemtime($filepath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
        ]);
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
        [
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
        ] = unserialize($str);
    }
}
