<?php

namespace Xsolve\Associate;

use Doctrine\ORM\EntityManagerInterface;
use Xsolve\Associate\AssociationCollecting\BasicAssociationCollectingStrategy;
use Xsolve\Associate\AssociationCollecting\DoctrineOrmAssociationCollectingStrategy;
use Xsolve\Associate\BufferedCollector\BufferedCollector;
use Xsolve\Associate\CollectionTraversal\ArrayCollectionTraversalStrategy;
use Xsolve\Associate\CollectionTraversal\DoctrineOrmCollectionCollectionTraversalStrategy;
use Xsolve\Associate\CollectionTraversal\TraversableCollectionTraversalStrategy;
use Xsolve\Associate\Loader\DoctrineOrmEntityLoader;
use Xsolve\Associate\Loader\DoctrineOrmNonProxiedAssociationQueryExecutor;
use Xsolve\Associate\Loader\DoctrineOrmUninitializedProxiesQueryExecutor;
use Xsolve\Associate\Metadata\MetadataWrapperProvider;

// TODO Make separate facade for Doctrine.
class Facade
{
    /**
     * @var EntityManagerInterface
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
     * @return BufferedCollector
     */
    public function getBufferedCollector(): BufferedCollector
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
     */
    protected function getMetadataWrapperProvider(): MetadataWrapperProvider
    {
        $this->assertEntityManagerAvailable();
        if (!$this->metadataWrapperProvider instanceof MetadataWrapperProvider) {
            $this->metadataWrapperProvider = new MetadataWrapperProvider($this->entityManager);
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
}
