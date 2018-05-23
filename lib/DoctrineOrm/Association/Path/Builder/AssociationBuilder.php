<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path\Builder;

use Xsolve\Associate\DoctrineOrm\Association\Path\Association;
use Xsolve\Associate\DoctrineOrm\Association\Path\DivergingAssociationPath;

class AssociationBuilder
{
    /**
     * @var Association
     */
    protected $association;

    /**
     * @var DivergingAssociationPathBuilder
     */
    protected $parentBuilder;

    /**
     * @param Association                     $association
     * @param DivergingAssociationPathBuilder $parentBuilder
     */
    public function __construct(Association $association, DivergingAssociationPathBuilder $parentBuilder)
    {
        $this->association = $association;
        $this->parentBuilder = $parentBuilder;
    }

    /**
     * @param string $alias
     *
     * @return self
     */
    public function aliasAs(string $alias): self
    {
        $this->association->setAlias($alias);

        return $this;
    }

    /**
     * @return self
     */
    public function loadFull(): self
    {
        $this->association->setLoadMode(Association::LOAD_MODE_FULL);

        return $this;
    }

    /**
     * @return self
     */
    public function loadProxy(): self
    {
        $this->association->setLoadMode(Association::LOAD_MODE_PROXY);

        return $this;
    }

    /**
     * @return self
     */
    public function loadNone(): self
    {
        $this->association->setLoadMode(Association::LOAD_MODE_NONE);

        return $this;
    }

    /**
     * @param string $relationshipName
     *
     * @return self
     *
     * @throws \Exception
     */
    public function associate(string $relationshipName): self
    {
        return $this->parentBuilder->associate($relationshipName);
    }

    /**
     * @return DivergingAssociationPathBuilder
     */
    public function diverge(): DivergingAssociationPathBuilder
    {
        return $this->parentBuilder->diverge();
    }

    /**
     * @return DivergingAssociationPathBuilder
     *
     * @throws \Exception
     */
    public function endDiverge(): DivergingAssociationPathBuilder
    {
        return $this->parentBuilder->endDiverge();
    }

    /**
     * @return DivergingAssociationPath
     */
    public function create(): DivergingAssociationPath
    {
        return $this->parentBuilder->create();
    }
}
