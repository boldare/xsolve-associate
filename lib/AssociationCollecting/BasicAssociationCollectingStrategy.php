<?php

namespace Xsolve\Associate\AssociationCollecting;

use Xsolve\Associate\CollectionTraversal\CollectionTraversalStrategyInterface;
use Xsolve\Associate\ObjectCollection\ObjectCollectionInterface;
use Xsolve\Associate\ObjectCollection\UniqueObjectCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class BasicAssociationCollectingStrategy implements AssociationCollectingStrategyInterface
{
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var CollectionTraversalStrategyInterface[]
     */
    protected $collectionTraversalStrategies = [];

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param CollectionTraversalStrategyInterface $collectionTraversalStrategy
     */
    public function addCollectionTraversalStrategy(CollectionTraversalStrategyInterface $collectionTraversalStrategy)
    {
        $this->collectionTraversalStrategies[] = $collectionTraversalStrategy;
    }

    /**
     * @param array  $objects
     * @param string $associationName
     *
     * @return array
     */
    public function collect(array $objects, string $associationName): array
    {
        $collectedAssociatedObjects = new UniqueObjectCollection();

        foreach ($objects as $object) {
            $associatedObject = $this->propertyAccessor->getValue($object, $associationName);
            if (!is_null($associatedObject)) {
                $this->addAssociatedObjectsToCollection($collectedAssociatedObjects, $associatedObject);
            }
        }

        return $collectedAssociatedObjects->getAll();
    }

    /**
     * @param ObjectCollectionInterface $objectCollection
     * @param mixed                     $propertyValue
     */
    protected function addAssociatedObjectsToCollection(ObjectCollectionInterface $objectCollection, $propertyValue)
    {
        $collectionTraversalStrategy = $this->getSupportingCollectionTraversalStrategy($propertyValue);
        if ($collectionTraversalStrategy instanceof CollectionTraversalStrategyInterface) {
            $collectionTraversalStrategy->traverse($objectCollection, $propertyValue);

            return;
        }

        $objectCollection->addOne($propertyValue);
    }

    /**
     * @param mixed $propertyValue
     *
     * @return CollectionTraversalStrategyInterface|null
     */
    protected function getSupportingCollectionTraversalStrategy($propertyValue)
    {
        foreach ($this->collectionTraversalStrategies as $collectionTraversalStrategy) {
            if ($collectionTraversalStrategy->supports($propertyValue)) {
                return $collectionTraversalStrategy;
            }
        }
    }
}
