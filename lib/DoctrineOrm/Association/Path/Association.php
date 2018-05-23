<?php

namespace Xsolve\Associate\DoctrineOrm\Association\Path;

class Association
{
    const LOAD_MODE_NONE = 'none';
    const LOAD_MODE_PROXY = 'proxy';
    const LOAD_MODE_FULL = 'full';

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
    protected $loadMode = 'none';

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
        return self::LOAD_MODE_FULL === $this->loadMode;
    }

    /**
     * @return bool
     */
    public function isLoadModeProxy(): bool
    {
        return self::LOAD_MODE_PROXY === $this->loadMode;
    }

    /**
     * @return bool
     */
    public function isLoadModeNone(): bool
    {
        return self::LOAD_MODE_NONE === $this->loadMode;
    }
}
