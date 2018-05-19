<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path\Builder;

use Xsolve\Associate\DoctrineOrm\Association\Path\Association;
use Xsolve\Associate\DoctrineOrm\Association\Path\AssociationPath;

class AssociationPathBuilder
{
    /**
     * @var AssociateCallTuple[]
     */
    protected $associateCallTuple = [];

    /**
     * @param string $relationshipName
     *
     * @return AssociationBuilder
     */
    public function associate(string $relationshipName): AssociationBuilder
    {
        $this->associateCallTuple[]
            = new AssociateCallTuple(
                $relationshipName,
                $associationBuilder = new AssociationBuilder($this)
            );

        return $associationBuilder;
    }

    /**
     * @return AssociationPath
     */
    public function create(): AssociationPath
    {
        $associationPath = new AssociationPath();

        foreach ($this->associateCallTuple as $associateCallTuple) {
            $association = new Association($associateCallTuple->getRelationshipName());
            $association->setLoadMode($associateCallTuple->getAssociationBuilder()->getLoadMode());
            $alias = $associateCallTuple->getAssociationBuilder()->getAlias();
            if ($alias) {
                $association->setAlias($alias);
            }
            $associationPath->addAssociation($association);
        }

        return $associationPath;
    }
}
