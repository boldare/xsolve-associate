<?php

namespace Xsolve\Associate;

use Doctrine\ORM\EntityManagerInterface;
use Xsolve\LegacyAssociate\AssociationCollecting\BasicAssociationCollectingStrategy;
use Xsolve\LegacyAssociate\AssociationCollecting\DoctrineOrmAssociationCollectingStrategy;
use Xsolve\LegacyAssociate\BufferedCollector\BufferedCollector;
use Xsolve\LegacyAssociate\BufferedCollector\BufferedCollectorInterface;
use Xsolve\LegacyAssociate\CollectionTraversal\ArrayCollectionTraversalStrategy;
use Xsolve\LegacyAssociate\CollectionTraversal\DoctrineOrmCollectionCollectionTraversalStrategy;
use Xsolve\LegacyAssociate\CollectionTraversal\TraversableCollectionTraversalStrategy;
use Xsolve\LegacyAssociate\Loader\DoctrineOrmEntityLoader;
use Xsolve\LegacyAssociate\Loader\DoctrineOrmNonProxiedAssociationQueryExecutor;
use Xsolve\LegacyAssociate\Loader\DoctrineOrmUninitializedProxiesQueryExecutor;
use Xsolve\LegacyAssociate\Metadata\MetadataWrapperProvider;

// TODO Make separate facade for Doctrine.
class Facade
{
    /**
     * @var EntityManagerInterface|null
     */
    protected $entityManager;

    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

    /**
     * @var BasicAssociationCollectingStrategy
     */
    protected $basicAssociationCollectingStrategy;

    /**
     * @var DoctrineOrmAssociationCollectingStrategy
     */
    protected $doctrineOrmAssociationCollectingStrategy;

    /**
     * @var DoctrineOrmEntityLoader
     */
    protected $doctrineOrmEntityLoader;

    /**
     * @var CollectorInterface
     */
    protected $basicCollector;

    /**
     * @var CollectorInterface
     */
    protected $doctrineOrmCollector;

    /**
     * @var BufferedCollector
     */
    protected $bufferedCollector;

    /**
     * @param EntityManagerInterface|null $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return CollectorInterface
     */
    public function getBasicCollector(): CollectorInterface
    {
        if (!$this->basicCollector instanceof CollectorInterface) {
            $this->basicCollector = new Collector(
                $this->getBasicAssociationCollectingStrategy()
            );
        }

        return $this->basicCollector;
    }

    /**
     * @return CollectorInterface
     */
    public function getDoctrineOrmCollector(): CollectorInterface
    {
        if (!$this->doctrineOrmCollector instanceof CollectorInterface) {
            $this->doctrineOrmCollector = new Collector(
                $this->getDoctrineOrmAssociationCollectingStrategy()
            );
        }

        return $this->doctrineOrmCollector;
    }

    /**
     * @return BufferedCollectorInterface
     */
    public function getBufferedDoctrineOrmCollector(): BufferedCollectorInterface
    {
        if (!$this->bufferedCollector instanceof BufferedCollector) {
            $this->bufferedCollector = new BufferedCollector(
                $this->getMetadataWrapperProvider(),
                $this->getDoctrineOrmCollector()
            );
        }

        return $this->bufferedCollector;
    }

    /**
     * @return MetadataWrapperProvider
     *
     * @throws \Exception
     */
    protected function getMetadataWrapperProvider(): MetadataWrapperProvider
    {
        $entityManager = $this->getEntityManager();
        if (!$this->metadataWrapperProvider instanceof MetadataWrapperProvider) {
            $this->metadataWrapperProvider = new MetadataWrapperProvider($entityManager);
        }

        return $this->metadataWrapperProvider;
    }

    /**
     * @return BasicAssociationCollectingStrategy
     */
    protected function getBasicAssociationCollectingStrategy(): BasicAssociationCollectingStrategy
    {
        if (!$this->basicAssociationCollectingStrategy instanceof BasicAssociationCollectingStrategy) {
            $this->basicAssociationCollectingStrategy = new BasicAssociationCollectingStrategy();
            $this->basicAssociationCollectingStrategy->addCollectionTraversalStrategy(
                new ArrayCollectionTraversalStrategy()
            );
            $this->basicAssociationCollectingStrategy->addCollectionTraversalStrategy(
                new TraversableCollectionTraversalStrategy()
            );
        }

        return $this->basicAssociationCollectingStrategy;
    }

    /**
     * @return DoctrineOrmAssociationCollectingStrategy
     */
    protected function getDoctrineOrmAssociationCollectingStrategy(): DoctrineOrmAssociationCollectingStrategy
    {
        if (!$this->doctrineOrmAssociationCollectingStrategy instanceof DoctrineOrmAssociationCollectingStrategy) {
            $this->doctrineOrmAssociationCollectingStrategy = new DoctrineOrmAssociationCollectingStrategy(
                $this->getMetadataWrapperProvider(),
                $this->getDoctrineOrmEntityLoader()
            );
            $this->doctrineOrmAssociationCollectingStrategy->addCollectionTraversalStrategy(
                new ArrayCollectionTraversalStrategy()
            );
            $this->doctrineOrmAssociationCollectingStrategy->addCollectionTraversalStrategy(
                new TraversableCollectionTraversalStrategy()
            );
            $this->doctrineOrmAssociationCollectingStrategy->addCollectionTraversalStrategy(
                new DoctrineOrmCollectionCollectionTraversalStrategy()
            );
        }

        return $this->doctrineOrmAssociationCollectingStrategy;
    }

    /**
     * @return DoctrineOrmEntityLoader
     */
    protected function getDoctrineOrmEntityLoader(): DoctrineOrmEntityLoader
    {
        if (!$this->doctrineOrmEntityLoader instanceof DoctrineOrmEntityLoader) {
            $this->doctrineOrmEntityLoader = new DoctrineOrmEntityLoader(
                $this->getMetadataWrapperProvider(),
                new BasicAssociationCollectingStrategy(),
                new DoctrineOrmUninitializedProxiesQueryExecutor(),
                new DoctrineOrmNonProxiedAssociationQueryExecutor()
            );
        }

        return $this->doctrineOrmEntityLoader;
    }

    /**
     * @throws \Exception
     */
    protected function assertEntityManagerAvailable()
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \Exception('Entity manager not available.');
        }
    }

    /**
     * @return EntityManagerInterface
     *
     * @throws \Exception
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \Exception('Entity manager not available.');
        }

        return $this->entityManager;
    }
}
