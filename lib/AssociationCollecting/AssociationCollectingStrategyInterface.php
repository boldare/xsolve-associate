<?php

namespace Xsolve\Associate\AssociationCollecting;

interface AssociationCollectingStrategyInterface
{
    /**
     * @param array  $objects
     * @param string $associationName
     *
     * @return array
     */
    public function collect(array $objects, string $associationName): array;
}
