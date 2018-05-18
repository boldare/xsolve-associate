<?php

namespace Xsolve\Associate\ObjectCollection;

interface ObjectCollectionInterface
{
    /**
     * @param mixed $object
     */
    public function addOne($object);

    /**
     * @param array $objects
     */
    public function addMany(array $objects);

    /**
     * @return array
     */
    public function getAll(): array;
}
