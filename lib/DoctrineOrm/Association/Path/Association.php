<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path;

class Association
{
    /**
     * @var string
     */
    protected $relationshipName;

    /**
     * @var ?string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $loadMode = 'proxy';

    /**
     * AssociationPath constructor.
     *
     * @param string $relationshipName
     */
    public function __construct(string $relationshipName)
    {
        $this->relationshipName = $relationshipName;
    }

    /**
     * @return string
     */
    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }

    /**
     * @param mixed $alias
     *
     * @return self
     */
    public function setAlias($alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $loadMode
     *
     * @return self
     */
    public function setLoadMode(string $loadMode): self
    {
        $this->loadMode = $loadMode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoadModeFull(): bool
    {
        return 'full' === $this->loadMode;
    }

    /**
     * @return bool
     */
    public function isLoadModeProxy(): bool
    {
        return 'proxy' === $this->loadMode;
    }
}
