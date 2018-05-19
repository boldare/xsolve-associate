<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path\Builder;

class AssociateCallTuple
{
    /**
     * @var string
     */
    protected $relationshipName;

    /**
     * @var AssociationBuilder
     */
    protected $associationBuilder;

    /**
     * @param string             $relationshipName
     * @param AssociationBuilder $associationBuilder
     */
    public function __construct(string $relationshipName, AssociationBuilder $associationBuilder)
    {
        $this->relationshipName = $relationshipName;
        $this->associationBuilder = $associationBuilder;
    }

    /**
     * @return string
     */
    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }

    /**
     * @return AssociationBuilder
     */
    public function getAssociationBuilder(): AssociationBuilder
    {
        return $this->associationBuilder;
    }
}
