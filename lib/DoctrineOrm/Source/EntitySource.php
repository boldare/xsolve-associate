<?php

namespace Xsolve\Associate\DoctrineOrm\Source;

use Xsolve\Associate\DoctrineOrm\Metadata\ClassMetadataAdapter;

class EntitySource
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @var string|null
     */
    protected $entityClass;

    /**
     * @var ClassMetadataAdapter|null
     */
    protected $classMetadataAdapter;

    /**
     * @param array       $entities
     * @param null|string $declaredEntityClass
     */
    public function __construct(array $entities, ?string $declaredEntityClass = null)
    {
        $this->entities = $entities;
        $this->entityClass = $declaredEntityClass;
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return bool
     */
    public function hasEntityClass(): bool
    {
        return null !== $this->entityClass;
    }

    /**
     * @return string|null
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    /**
     * @param ClassMetadataAdapter $classMetadataAdapter
     */
    public function setClassMetadataAdapter(ClassMetadataAdapter $classMetadataAdapter): void
    {
        $this->classMetadataAdapter = $classMetadataAdapter;
    }

    /**
     * @return ClassMetadataAdapter|null
     */
    public function getClassMetadataAdapter(): ?ClassMetadataAdapter
    {
        return $this->classMetadataAdapter;
    }
}
