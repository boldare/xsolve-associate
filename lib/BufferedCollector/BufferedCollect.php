<?php

namespace Xsolve\Associate\BufferedCollector;

use Xsolve\Associate\AssociationPath\AssociationPath;

class BufferedCollect
{
    /**
     * @var array
     */
    protected $objects;

    /**
     * @var AssociationPath
     */
    protected $associationPath;

    /**
     * @var array|null
     */
    protected $associatedEntities;

    /**
     * @param array     $objects
     * @param string[]  $associationNames
     */
    public function __construct(
        array $objects,
        array $associationNames
    ) {
        $this->objects = $objects;
        $this->associationPath = new AssociationPath($associationNames);
    }

    /**
     * @return array
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @return AssociationPath
     */
    public function getAssociationPath(): AssociationPath
    {
        return $this->associationPath;
    }

    /**
     * @param array $associatedEntities
     *
     * @throws \Exception
     */
    public function setAssociatedEntities(array $associatedEntities)
    {
        if (!is_null($this->associatedEntities)) {
            throw new \Exception('Associated entities were already set.');
        }

        $this->associatedEntities = $associatedEntities;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getAssociatedEntities(): array
    {
        if (is_null($this->associatedEntities)) {
            throw new \Exception('Associated entities were not set.');
        }

        return $this->associatedEntities;
    }
}
