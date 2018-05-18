<?php

namespace Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table(name="category")
 */
class Category
{
    /**
     * @var string
     *
     * @Id
     * @Column(type="string", length=64)
     */
    protected $id;

    /**
     * @var string
     *
     * @Column(type="string", length=128)
     */
    protected $name;

    /**
     * @var Collection|Product[]
     *
     * @OneToMany(targetEntity="Product", mappedBy="category")
     */
    protected $products;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;

        $this->products = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param Product[] $products
     */
    public function setProducts(array $products)
    {
        $this->products->clear();
        foreach ($products as $product) {
            $this->products->add($product);
        }
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
}
