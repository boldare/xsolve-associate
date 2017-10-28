<?php

namespace Xsolve\AssociateTests\Functional\Model;

class Bar
{
    /**
     * @var Baz[]
     */
    public $bazs;

    /**
     * @param Baz[] $bazs
     */
    public function __construct(array $bazs)
    {
        $this->bazs = $bazs;
    }
}
