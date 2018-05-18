<?php

namespace Xsolve\LegacyAssociate\Loader;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy;
use Xsolve\LegacyAssociate\AssociationCollecting\AssociationCollectingStrategyInterface;
use Xsolve\LegacyAssociate\Metadata\AssociationMetadataWrapper;
use Xsolve\LegacyAssociate\Metadata\ClassMetadataWrapper;
use Xsolve\LegacyAssociate\Metadata\MetadataWrapperProvider;

class DoctrineOrmEntityLoader
{
    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

    /**
     * @var AssociationCollectingStrategyInterface
     */
    protected $associationCollectingStrategy;

    /**
     * @var DoctrineOrmUninitializedProxiesQueryExecutor
     */
    protected $uninitializedProxiesQueryExecutor;

    /**
     * @var DoctrineOrmNonProxiedAssociationQueryExecutor
     */
    protected $nonProxiedAssociationQueryExecutor;

    /**
     * @var int|null
     */
    protected $chunkSize = 1000;

    /**
     * @param MetadataWrapperProvider                       $metadataWrapperProvider
     * @param AssociationCollectingStrategyInterface        $associationCollectingStrategy
     * @param DoctrineOrmUninitializedProxiesQueryExecutor  $uninitializedProxiesQueryExecutor
     * @param DoctrineOrmNonProxiedAssociationQueryExecutor $nonProxiedAssociationQueryExecutor
     */
    public function __construct(
        MetadataWrapperProvider $metadataWrapperProvider,
        AssociationCollectingStrategyInterface $associationCollectingStrategy,
        DoctrineOrmUninitializedProxiesQueryExecutor $uninitializedProxiesQueryExecutor,
        DoctrineOrmNonProxiedAssociationQueryExecutor $nonProxiedAssociationQueryExecutor
    ) {
        $this->metadataWrapperProvider = $metadataWrapperProvider;
        $this->associationCollectingStrategy = $associationCollectingStrategy;
        $this->uninitializedProxiesQueryExecutor = $uninitializedProxiesQueryExecutor;
        $this->nonProxiedAssociationQueryExecutor = $nonProxiedAssociationQueryExecutor;
    }

    /**
     * @param int|null $chunkSize
     */
    public function setChunkSize(int $chunkSize = null)
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * @param array                $entities
     * @param ClassMetadataWrapper $classMetadataWrapper
     */
    public function loadUninitializedProxies(array $entities, ClassMetadataWrapper $classMetadataWrapper)
    {
        $uninitializedProxies = $this->getUninitializedProxies($entities);

        if (empty($uninitializedProxies)) {
            return;
        }

        foreach ($this->splitIntoChunks($uninitializedProxies) as $uninitializedProxiesChunk) {
            $this->uninitializedProxiesQueryExecutor->execute($uninitializedProxiesChunk, $classMetadataWrapper);
        }
    }

    /**
     * @param array                      $entities
     * @param AssociationMetadataWrapper $associationMetadataWrapper
     *
     * @throws \Exception
     */
    public function loadAssociatedUninitializedCollectionsAndProxies(
        array $entities,
        AssociationMetadataWrapper $associationMetadataWrapper
    ) {
        // If we have to-one association and we are on the owning side
        // we can collect uninitialized proxies and bulk load them.
        if (
            $associationMetadataWrapper->isManyToOne()
            || (
                $associationMetadataWrapper->isOneToOne()
                && $associationMetadataWrapper->isOwningSide()
            )
        ) {
            $associatedEntities = $this->associationCollectingStrategy->collect(
                $entities,
                $associationMetadataWrapper->getName()
            );
            $this->loadUninitializedProxies(
                $associatedEntities,
                $associationMetadataWrapper->getTargetClassMetadataWrapper()
            );

            return;
        }

        // Otherwise we have some objects with uninitialized persistent collections.
        if (
            $associationMetadataWrapper->isOneToMany()
            || $associationMetadataWrapper->isManyToMany()
        ) {
            $associatedCollections = $this->associationCollectingStrategy->collect(
                $entities,
                $associationMetadataWrapper->getName()
            );
            $uninitializedSourceEntities = $this->getUninitializedCollectionOwnerEntities($associatedCollections);

            foreach ($this->splitIntoChunks($uninitializedSourceEntities) as $uninitializedSourceEntitiesChunk) {
                $this->nonProxiedAssociationQueryExecutor->execute(
                    $uninitializedSourceEntitiesChunk,
                    $associationMetadataWrapper
                );
            }

            return;
        }

        // Or we have one-to-one association with source being the inverse side.
        if (
            $associationMetadataWrapper->isOneToOne()
            && $associationMetadataWrapper->isInverseSide()
        ) {
            // We don't have to do anything as these objects are automatically loaded by Doctrine with separate queries
            // and there's no way to optimize this.

            return;
        }

        throw new \Exception('Association not handled.');
    }

    /**
     * @param array $entities
     *
     * @return Proxy[]
     */
    protected function getUninitializedProxies(array $entities): array
    {
        return array_filter(
            $entities,
            function ($entity) {
                return $entity instanceof Proxy && !$entity->__isInitialized();
            }
        );
    }

    /**
     * @param array $collections
     *
     * @return Proxy[]
     */
    protected function getUninitializedCollectionOwnerEntities(array $collections): array
    {
        return array_filter(
            array_map(
                function (Collection $collection) {
                    if (
                        $collection instanceof PersistentCollection
                        && !$collection->isInitialized()
                    ) {
                        return $collection->getOwner();
                    }
                },
                $collections
            )
        );
    }

    /**
     * @param array $items
     *
     * @return array
     */
    protected function splitIntoChunks(array $items): array
    {
        if (is_null($this->chunkSize)) {
            return $items;
        }

        return array_chunk($items, $this->chunkSize);
    }
}
