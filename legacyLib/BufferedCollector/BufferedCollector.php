<?php

namespace Xsolve\LegacyAssociate\BufferedCollector;

use Xsolve\LegacyAssociate\CollectorInterface;
use Xsolve\LegacyAssociate\Metadata\MetadataWrapperProvider;
use Xsolve\LegacyAssociate\ObjectCollection\UniqueObjectCollection;

class BufferedCollector implements BufferedCollectorInterface
{
    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

    /**
     * @var CollectorInterface
     */
    protected $collector;

    /**
     * @var BufferedCollect[]
     */
    protected $bufferedCollects = [];

    /**
     * @param MetadataWrapperProvider $metadataWrapperProvider
     * @param CollectorInterface      $collector
     */
    public function __construct(
        MetadataWrapperProvider $metadataWrapperProvider,
        CollectorInterface $collector
    ) {
        $this->metadataWrapperProvider = $metadataWrapperProvider;
        $this->collector = $collector;
    }

    /**
     * @param array    $objects
     * @param string[] $associationPath
     *
     * @return \Closure
     */
    public function createCollectClosure(array $objects, array $associationPath): \Closure
    {
        $bufferedCollect = new BufferedCollect($objects, $associationPath);
        $this->bufferedCollects[] = $bufferedCollect;

        return function () use ($bufferedCollect) {
            if (!empty($this->bufferedCollects)) {
                $this->resolveSimilar($bufferedCollect);
            }

            return $bufferedCollect->getAssociatedEntities();
        };
    }

    /**
     * @param BufferedCollect $resolvedBufferedCollect
     */
    protected function resolveSimilar(BufferedCollect $resolvedBufferedCollect)
    {
        $resolvedAssociationPath = $resolvedBufferedCollect->getAssociationPath();

        $objects = new UniqueObjectCollection();
        $similarBufferedCollects = [];
        foreach ($this->bufferedCollects as $bufferedCollectKey => $bufferedCollect) {
            if ($bufferedCollect->getAssociationPath()->isEqual($resolvedAssociationPath)) {
                $similarBufferedCollects[] = $bufferedCollect;
                $objects->addMany($bufferedCollect->getObjects());
                unset($this->bufferedCollects[$bufferedCollectKey]);
            }
        }

        // TODO We would only need to fetch as far as last Doctrine association is in case of mixed associations.

        $this->collector->collect($objects->getAll(), $resolvedAssociationPath->getAssociationNames());
        foreach ($similarBufferedCollects as $bufferedCollect) {
            $bufferedCollect->setAssociatedEntities(
                $this->collector->collect($bufferedCollect->getObjects(), $resolvedAssociationPath->getAssociationNames())
            );
        }
    }
}
