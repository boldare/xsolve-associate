<?php

namespace Xsolve\LegacyAssociate\CollectionTraversal;

use Xsolve\LegacyAssociate\ObjectCollection\ObjectCollectionInterface;

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
        /* @var \Traversable $propertyValue */
        $objectCollection->addMany(iterator_to_array($propertyValue));
    }
}
