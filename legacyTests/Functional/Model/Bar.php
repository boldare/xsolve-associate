<?php

namespace Xsolve\LegacyAssociateTests\Functional\Model;

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
