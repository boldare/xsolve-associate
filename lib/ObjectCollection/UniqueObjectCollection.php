<?php

namespace Xsolve\Associate\ObjectCollection;

class UniqueObjectCollection implements ObjectCollectionInterface
{
    /**
     * @var \SplObjectStorage
     */
    protected $objects;

    public function __construct()
    {
        $this->objects = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function addOne($object)
    {
        if (!is_object($object)) {
            $object = new NonObjectWrapper($object);
        }

        $this->objects->attach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function addMany(array $objects)
    {
        foreach ($objects as $object) {
            $this->addOne($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        $objects = [];
        foreach ($this->objects as $object) {
            $objects[] = ($object instanceof NonObjectWrapper)
                ? $object->unwrap()
                : $object;
        }

        return $objects;
    }
}
