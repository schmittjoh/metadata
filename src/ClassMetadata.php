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
    use SerializationHelper;

    /**
     * @var string
     */
    public $name;

    /**
     * @var MethodMetadata[]
     */
    public $methodMetadata = [];

    /**
     * @var PropertyMetadata[]
     */
    public $propertyMetadata = [];

    /**
     * @var string[]
     */
    public $fileResources = [];

    /**
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

    protected function serializeToArray(): array
    {
        return [
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
        ];
    }

    protected function unserializeFromArray(array $data): void
    {
        [
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
        ] = $data;
    }
}
