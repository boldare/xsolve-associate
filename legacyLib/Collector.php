<?php

namespace Xsolve\Associate;

use Xsolve\LegacyAssociate\AssociationCollecting\AssociationCollectingStrategyInterface;

class Collector implements CollectorInterface
{
    /**
     * @var AssociationCollectingStrategyInterface
     */
    protected $associationCollectingStrategy;

    /**
     * @param AssociationCollectingStrategyInterface $associationCollectingStrategy
     */
    public function __construct(AssociationCollectingStrategyInterface $associationCollectingStrategy)
    {
        $this->associationCollectingStrategy = $associationCollectingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(array $objects, array $associationPath): array
    {
        foreach ($associationPath as $associationName) {
            $objects = $this->associationCollectingStrategy->collect($objects, $associationName);
        }

        return $objects;
    }
}
