<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path;

class DivergingAssociationPath extends AssociationPath
{
    /**
     * @var self[]
     */
    protected $childDivergingAssociationPaths = [];

    /**
     * @param self $childDivergingAssociationPath
     */
    public function addChildDivergingAsociationPath(self $childDivergingAssociationPath): void
    {
        $this->childDivergingAssociationPaths[] = $childDivergingAssociationPath;
    }

    /**
     * @return bool
     */
    public function hasChildDivergingAssociationPath(): bool
    {
        return (bool) $this->childDivergingAssociationPaths;
    }

    /**
     * @return self[]
     */
    public function getChildDivergingAssociationPaths(): array
    {
        return $this->childDivergingAssociationPaths;
    }
}
