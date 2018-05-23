<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path\Builder;

use Xsolve\Associate\DoctrineOrm\Association\Path\Association;
use Xsolve\Associate\DoctrineOrm\Association\Path\DivergingAssociationPath;

class DivergingAssociationPathBuilder
{
    /**
     * @var self[]
     */
    protected $childBuilders = [];

    /**
     * @var DivergingAssociationPath
     */
    protected $divergingAssociationPath;

    /**
     * @var self|null
     */
    protected $parentBuilder;

    /**
     * @param DivergingAssociationPath|null $divergingAssociationPath
     * @param self|null                     $parentBuilder
     */
    public function __construct(?DivergingAssociationPath $divergingAssociationPath = null, ?self $parentBuilder = null)
    {
        $this->divergingAssociationPath = $divergingAssociationPath instanceof DivergingAssociationPath
            ? $divergingAssociationPath
            : new DivergingAssociationPath();
        $this->parentBuilder = $parentBuilder;
    }

    /**
     * @param string $relationshipName
     *
     * @return AssociationBuilder
     *
     * @throws \Exception
     */
    public function associate(string $relationshipName): AssociationBuilder
    {
        if ($this->divergingAssociationPath->hasChildDivergingAssociationPath()) {
            throw new \Exception();
        }

        $association = new Association($relationshipName);
        $this->divergingAssociationPath->addAssociation($association);

        return new AssociationBuilder($association, $this);
    }

    /**
     * @return self
     */
    public function diverge(): self
    {
        $childDivergingAssociationPath = new DivergingAssociationPath();
        $this->divergingAssociationPath->addChildDivergingAsociationPath($childDivergingAssociationPath);

        return new self($childDivergingAssociationPath, $this);
    }

    /**
     * @return self
     *
     * @throws \Exception
     */
    public function endDiverge(): self
    {
        if (!$this->parentBuilder instanceof self) {
            throw new \Exception();
        }

        return $this->parentBuilder;
    }

    /**
     * @return DivergingAssociationPath
     */
    public function create(): DivergingAssociationPath
    {
        if ($this->parentBuilder instanceof self) {
            return $this->parentBuilder->create();
        }

        return $this->divergingAssociationPath;
    }
}
