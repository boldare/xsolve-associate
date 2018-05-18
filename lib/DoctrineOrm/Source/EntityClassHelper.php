<?php

namespace Xsolve\Associate\DoctrineOrm\Source;

use Xsolve\Associate\DoctrineOrm\Metadata\ClassMetadataAdapter;
use Xsolve\Associate\DoctrineOrm\Metadata\MetadataAdapterProvider;

class EntityClassHelper
{
    /**
     * @var MetadataAdapterProvider
     */
    protected $metadataAdapterProvider;

    /**
     * @param MetadataAdapterProvider $metadataAdapterProvider
     */
    public function __construct(MetadataAdapterProvider $metadataAdapterProvider)
    {
        $this->metadataAdapterProvider = $metadataAdapterProvider;
    }

    /**
     * @param EntitySource $entitySource
     *
     * @throws \Exception if one common entity class cannot be determined
     */
    public function supplementEntitySource(EntitySource $entitySource): void
    {
        if ($entitySource->hasEntityClass()) {
            return;
        }

        $entityClassName = $this->metadataAdapterProvider->getEntityClassForEntities($entitySource->getEntities());
        $entitySource->setEntityClass($entityClassName);

        $classMetadataAdapter = $this->metadataAdapterProvider->getClassMetadataAdapterByClassName($entityClassName);
        if (!$classMetadataAdapter instanceof ClassMetadataAdapter) {
            throw new \Exception();
        }
        $entitySource->setClassMetadataAdapter($classMetadataAdapter);
    }
}
