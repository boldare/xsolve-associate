<?php

namespace Xsolve\AssociateTests\Functional\DoctrineOrm\Mock;

interface DataProviderInterface
{
    /**
     * @return array
     */
    public function getUsers(array $ids = null): array;

    /**
     * @return array
     */
    public function getStores(array $ids = null): array;

    /**
     * @return array
     */
    public function getProducts(array $ids = null): array;

    /**
     * @return array
     */
    public function getVariants(array $ids = null): array;

    /**
     * @return array
     */
    public function getCategories(array $ids = null): array;

    /**
     * @return array
     */
    public function getAll(): array;
}
