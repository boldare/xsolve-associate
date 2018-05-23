<?php

namespace Xsolve\LegacyAssociate\AssociationCollecting;

use Xsolve\LegacyAssociate\Loader\DoctrineOrmEntityLoader;
use Xsolve\LegacyAssociate\Metadata\AssociationMetadataWrapper;
use Xsolve\LegacyAssociate\Metadata\ClassMetadataWrapper;
use Xsolve\LegacyAssociate\Metadata\MetadataWrapperProvider;

class DoctrineOrmAssociationCollectingStrategy extends BasicAssociationCollectingStrategy
{
    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

    /**
     * @var DoctrineOrmEntityLoader
     */
    protected $doctrineOrmEntityLoader;

    /**
     * @param MetadataWrapperProvider $metadataWrapperProvider
     * @param DoctrineOrmEntityLoader $doctrineOrmEntityLoader
     */
    public function __construct(
        MetadataWrapperProvider $metadataWrapperProvider,
        DoctrineOrmEntityLoader $doctrineOrmEntityLoader
    ) {
        parent::__construct();

        $this->metadataWrapperProvider = $metadataWrapperProvider;
        $this->doctrineOrmEntityLoader = $doctrineOrmEntityLoader;
    }

    /**
     * @param array  $objects
     * @param string $associationName
     *
     * @return array
     */
    public function collect(array $objects, string $associationName): array
    {
        $this->loadObjectsIfPossible($objects, $associationName);

        return parent::collect($objects, $associationName);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadObjectsIfPossible(array $objects, string $associationName)
    {
        $classMetadataWrapper = $this->metadataWrapperProvider->getClassMetadataWrapperByObjects($objects);
        if (!$classMetadataWrapper instanceof ClassMetadataWrapper) {
            return;
        }

        $this->doctrineOrmEntityLoader->loadUninitializedProxies($objects, $classMetadataWrapper);

        $associationMetadataWrapper = $classMetadataWrapper->getAssociationMetadataWrapper($associationName);
        if (!$associationMetadataWrapper instanceof AssociationMetadataWrapper) {
            return;
        }

        $this->doctrineOrmEntityLoader->loadAssociatedUninitializedCollectionsAndProxies(
            $objects,
            $associationMetadataWrapper
        );
    }
}
