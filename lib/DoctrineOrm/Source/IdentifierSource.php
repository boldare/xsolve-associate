<?php

namespace Xsolve\Associate\DoctrineOrm\Source;

class IdentifierSource
{
    /**
     * @var array
     */
    protected $identifiers;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @param array  $identifiers
     * @param string $entityClass
     */
    public function __construct(array $identifiers, string $entityClass)
    {
        $this->identifiers = $identifiers;
        $this->entityClass = $entityClass;
    }

    /**
     * @return array
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
