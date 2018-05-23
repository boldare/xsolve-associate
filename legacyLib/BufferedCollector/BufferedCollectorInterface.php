<?php

namespace Xsolve\LegacyAssociate\BufferedCollector;

interface BufferedCollectorInterface
{
    /**
     * @param array    $objects
     * @param string[] $associationPath
     *
     * @return \Closure
     */
    public function createCollectClosure(array $objects, array $associationPath): \Closure;
}
