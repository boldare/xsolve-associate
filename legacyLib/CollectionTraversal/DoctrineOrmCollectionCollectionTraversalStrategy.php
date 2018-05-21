<?php

namespace Xsolve\LegacyAssociate\CollectionTraversal;

use Doctrine\Common\Collections\Collection;
use Xsolve\LegacyAssociate\ObjectCollection\ObjectCollectionInterface;

class DoctrineOrmCollectionCollectionTraversalStrategy implements CollectionTraversalStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($propertyValue): bool
    {
        return $propertyValue instanceof Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function traverse(ObjectCollectionInterface $objectCollection, $propertyValue)
    {
        /* @var Collection $propertyValue */
        $objectCollection->addMany($propertyValue->getValues());
    }
}
