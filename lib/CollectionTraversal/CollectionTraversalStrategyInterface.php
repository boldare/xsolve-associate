<?php

namespace Xsolve\Associate\CollectionTraversal;

use Xsolve\Associate\ObjectCollection\ObjectCollectionInterface;

interface CollectionTraversalStrategyInterface
{
    /**
     * @param mixed $propertyValue
     *
     * @return bool
     */
    public function supports($propertyValue): bool;

    /**
     * @param ObjectCollectionInterface $objectCollection
     * @param mixed                     $propertyValue
     */
    public function traverse(ObjectCollectionInterface $objectCollection, $propertyValue);
}
