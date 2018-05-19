<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path\Builder;

use Xsolve\Associate\DoctrineOrm\Association\Path\AssociationPath;

class AssociationBuilder
{
    /**
     * @var AssociationPathBuilder
     */
    protected $rootBuilder;

    /**
     * @var string
     */
    protected $loadMode = 'proxy';

    /**
     * @var ?string
     */
    protected $alias;

    /**
     * @param AssociationPathBuilder $rootBuilder
     */
    public function __construct(AssociationPathBuilder $rootBuilder)
    {
        $this->rootBuilder = $rootBuilder;
    }

    /**
     * @param string $alias
     *
     * @return self
     */
    public function aliasAs(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return self
     */
    public function loadFull(): self
    {
        $this->loadMode = 'full';

        return $this;
    }

    /**
     * @param string $relationshipName
     *
     * @return self
     */
    public function associate(string $relationshipName): self
    {
        return $this->rootBuilder->associate($relationshipName);
    }

    /**
     * @return AssociationPath
     */
    public function create(): AssociationPath
    {
        return $this->rootBuilder->create();
    }

    /**
     * TODO Should it be moved somewhere else?
     *
     * @return string
     */
    public function getLoadMode(): string
    {
        return $this->loadMode;
    }

    /**
     * TODO Should it be moved somewhere else?
     *
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
