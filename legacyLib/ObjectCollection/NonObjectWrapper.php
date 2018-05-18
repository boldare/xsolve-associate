<?php

namespace Xsolve\LegacyAssociate\ObjectCollection;

class NonObjectWrapper
{
    /**
     * @var mixed
     */
    protected $nonObject;

    /**
     * @param mixed $nonObject
     */
    public function __construct($nonObject)
    {
        $this->nonObject = $nonObject;
    }

    /**
     * @return mixed
     */
    public function unwrap()
    {
        return $this->nonObject;
    }
}
