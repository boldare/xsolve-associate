<?php

namespace Xsolve\LegacyAssociateTests\Functional\Model;

class Foo
{
    /**
     * @var Bar|null
     */
    protected $bar;

    /**
     * @param Bar $bar
     */
    public function __construct(Bar $bar = null)
    {
        $this->bar = $bar;
    }

    /**
     * @return Bar|null
     */
    public function getBar()
    {
        return $this->bar;
    }
}
