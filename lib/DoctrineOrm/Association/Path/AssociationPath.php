<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path;

class AssociationPath
{
    /**
     * @var array
     */
    protected $associations = [];

    /**
     * @param Association $association
     *
     * @return self
     */
    public function addAssociation(Association $association): self
    {
        $this->associations[] = $association;

        return $this;
    }
}
