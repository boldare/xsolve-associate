<?php

namespace Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity
 * @Table(name="variant")
 */
class Variant
{
    /**
     * @var string
     *
     * @Id
     * @Column(type="string", length=64)
     */
    protected $id;

    /**
     * @var int
     *
     * @Column(type="integer")
     */
    protected $price;

    /**
     * @var Product
     *
     * @ManyToOne(targetEntity="Product", inversedBy="products")
     */
    protected $product;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
}
