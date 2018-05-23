<?php

namespace Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock;

use Xsolve\LegacyAssociateTests\Functional\DataProviderInterface;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Category;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Product;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Store;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\User;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Variant;
use Symfony\Component\Yaml\Yaml;

class DataProvider implements DataProviderInterface
{
    /**
     * @var User[]
     */
    protected $usersMap = [];

    /**
     * @var Store[]
     */
    protected $storesMap = [];

    /**
     * @var Product[]
     */
    protected $productsMap = [];

    /**
     * @var Variant[]
     */
    protected $variantsMap = [];

    /**
     * @var Category[]
     */
    protected $categoriesMap = [];

    public function __construct()
    {
        $data = Yaml::parse(
            file_get_contents(__DIR__ . '/../../data.yml')
        );

        $categoriesMap = [];
        foreach ($data['categories'] as $categoryData) {
            $category = new Category($categoryData['id']);
            $category->setName($categoryData['name']);
            $this->categoriesMap[$category->getId()] = $category;
        }

        foreach ($data['users'] as $userData) {
            $user = new User($userData['id']);
            foreach ($userData['stores'] as $storeData) {
                $store = new Store($storeData['id']);
                $store->setUser($user);
                $user->addStore($store);
                $store->setApproved($storeData['approved']);
                foreach ($storeData['products'] as $productData) {
                    $product = new Product($productData['id']);
                    $product->setStore($store);
                    $store->addProduct($product);
                    $product->setApproved($productData['approved']);
                    $product->setName($productData['name']);
                    $product->setCategory($this->categoriesMap[$productData['categoryId']]);
                    foreach ($productData['variants'] as $variantData) {
                        $variant = new Variant($variantData['id']);
                        $variant->setProduct($product);
                        $product->addVariant($variant);
                        $variant->setPrice($variantData['price']);
                        $this->variantsMap[$variant->getId()] = $variant;
                    }
                    $this->productsMap[$product->getId()] = $product;
                }
                $this->storesMap[$store->getId()] = $store;
            }
            $this->usersMap[$user->getId()] = $user;
        }
    }

    /**
     * @return User[]
     */
    public function getUsers(array $ids = null): array
    {
        if (is_array($ids)) {
            return array_values(
                array_intersect_key(
                    $this->usersMap,
                    array_flip($ids)
                )
            );
        }

        return array_values($this->usersMap);
    }

    /**
     * @return Store[]
     */
    public function getStores(array $ids = null): array
    {
        if (is_array($ids)) {
            return array_values(
                array_intersect_key(
                    $this->storesMap,
                    array_flip($ids)
                )
            );
        }

        return array_values($this->storesMap);
    }

    /**
     * @return Product[]
     */
    public function getProducts(array $ids = null): array
    {
        if (is_array($ids)) {
            return array_values(
                array_intersect_key(
                    $this->productsMap,
                    array_flip($ids)
                )
            );
        }

        return array_values($this->productsMap);
    }

    /**
     * @return Variant[]
     */
    public function getVariants(array $ids = null): array
    {
        if (is_array($ids)) {
            return array_values(
                array_intersect_key(
                    $this->variantsMap,
                    array_flip($ids)
                )
            );
        }

        return array_values($this->variantsMap);
    }

    /**
     * @return Category[]
     */
    public function getCategories(array $ids = null): array
    {
        if (is_array($ids)) {
            return array_values(
                array_intersect_key(
                    $this->categoriesMap,
                    array_flip($ids)
                )
            );
        }

        return array_values($this->categoriesMap);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return array_merge(
            $this->getCategories(),
            $this->getUsers(),
            $this->getStores(),
            $this->getProducts(),
            $this->getVariants()
        );
    }
}
