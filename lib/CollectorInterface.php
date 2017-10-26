<?php

namespace Xsolve\Associate;

interface CollectorInterface
{
    /**
     * @param array    $objects
     * @param string[] $associationPath
     *
     * @return array
     */
    public function collect(array $objects, array $associationPath): array;
}
