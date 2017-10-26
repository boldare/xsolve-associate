<?php

namespace Xsolve\Associate\CollectionTraversal;

use Xsolve\Associate\ObjectCollection\ObjectCollectionInterface;

class TraversableCollectionTraversalStrategy implements CollectionTraversalStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($propertyValue): bool
    {
        return $propertyValue instanceof \Traversable;
    }

    /**
     * {@inheritdoc}
     */
    public function traverse(ObjectCollectionInterface $objectCollection, $propertyValue)
    {
        /* @var array $propertyValue */
        $objectCollection->addMany(iterator_to_array($propertyValue));
    }
}
