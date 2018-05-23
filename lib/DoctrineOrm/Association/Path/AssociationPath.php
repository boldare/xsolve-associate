<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path;

class AssociationPath
{
    /**
     * @var Association[]
     */
    protected $associations = [];

    /**
     * @param Association $association
     */
    public function addAssociation(Association $association): void
    {
        $this->associations[] = $association;
    }

    /**
     * @return Association[]
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }
}
