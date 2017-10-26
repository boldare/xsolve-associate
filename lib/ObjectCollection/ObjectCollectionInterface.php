<?php

namespace Xsolve\Associate\ObjectCollection;

interface ObjectCollectionInterface
{
    /**
     * @param $object
     *
     * @return mixed
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
