<?php

namespace Xsolve\Associate\CollectionTraversal;

use Xsolve\Associate\ObjectCollection\ObjectCollectionInterface;

class ArrayCollectionTraversalStrategy implements CollectionTraversalStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($propertyValue): bool
    {
        if (!is_array($propertyValue)) {
            return false;
        }
        if (empty($propertyValue)) {
            return true;
        }

        return array_keys($propertyValue) === range(0, count($propertyValue) - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function traverse(ObjectCollectionInterface $objectCollection, $propertyValue)
    {
        /* @var array $propertyValue */
        $objectCollection->addMany($propertyValue);
    }
}
