<?php

namespace Xsolve\LegacyAssociate\Metadata;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class AssociationMetadataWrapper
{
    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

    /**
     * @var array
     */
    protected $associationMapping;

    /**
     * @param MetadataWrapperProvider $metadataWrapperProvider
     * @param array                   $associationMapping
     */
    public function __construct(
        MetadataWrapperProvider $metadataWrapperProvider,
        array $associationMapping
    ) {
        $this->metadataWrapperProvider = $metadataWrapperProvider;
        $this->associationMapping = $associationMapping;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->associationMapping['fieldName'];
    }

    /**
     * @return bool
     */
    public function isOwningSide(): bool
    {
        return $this->associationMapping['isOwningSide'];
    }

    /**
     * @return bool
     */
    public function isInverseSide(): bool
    {
        return !$this->isOwningSide();
    }

    /**
     * @return bool
     */
    public function isOneToOne(): bool
    {
        return ClassMetadataInfo::ONE_TO_ONE === $this->associationMapping['type'];
    }

    /**
     * @return bool
     */
    public function isOneToMany(): bool
    {
        return ClassMetadataInfo::ONE_TO_MANY === $this->associationMapping['type'];
    }

    /**
     * @return bool
     */
    public function isManyToOne(): bool
    {
        return ClassMetadataInfo::MANY_TO_ONE === $this->associationMapping['type'];
    }

    /**
     * @return bool
     */
    public function isManyToMany(): bool
    {
        return ClassMetadataInfo::MANY_TO_MANY === $this->associationMapping['type'];
    }

    /**
     * @return string
     */
    public function getSourceClassName(): string
    {
        return $this->associationMapping['sourceEntity'];
    }

    /**
     * @return ClassMetadataWrapper
     *
     * @throws \Exception
     */
    public function getSourceClassMetadataWrapper(): ClassMetadataWrapper
    {
        $sourceClassName = $this->metadataWrapperProvider->getClassMetadataWrapperByClassName($this->getSourceClassName());
        if (!$sourceClassName instanceof ClassMetadataWrapper) {
            throw new \Exception('Source class name not determined.');
        }

        return $sourceClassName;
    }

    /**
     * @return string
     */
    public function getTargetClassName(): string
    {
        return $this->associationMapping['targetEntity'];
    }

    /**
     * @return ClassMetadataWrapper
     *
     * @throws \Exception
     */
    public function getTargetClassMetadataWrapper(): ClassMetadataWrapper
    {
        $targetClassName = $this->metadataWrapperProvider->getClassMetadataWrapperByClassName($this->getTargetClassName());
        if (!$targetClassName instanceof ClassMetadataWrapper) {
            throw new \Exception('Target class name not determined.');
        }

        return $targetClassName;
    }
}
