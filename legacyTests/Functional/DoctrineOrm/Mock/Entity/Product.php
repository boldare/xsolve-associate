<?php

namespace Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table
 */
class Product
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
     * @var bool
     *
     * @Column(type="boolean")
     */
    protected $approved;

    /**
     * @var Store
     *
     * @ManyToOne(targetEntity="Store", inversedBy="products")
     */
    protected $store;

    /**
     * @var Category
     *
     * @ManyToOne(targetEntity="Category", inversedBy="products")
     */
    protected $category;

    /**
     * @var Collection|Variant[]
     *
     * @OneToMany(targetEntity="Variant", mappedBy="product")
     */
    protected $variants;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;

        $this->variants = new ArrayCollection();
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
     * @param bool $approved
     */
    public function setApproved(bool $approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param Store $store
     */
    public function setStore(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Variant[] $variants
     */
    public function setVariants(array $variants)
    {
        $this->variants->clear();
        foreach ($variants as $variant) {
            $this->variants->add($variant);
        }
    }

    /**
     * @param Variant $variant
     */
    public function addVariant(Variant $variant)
    {
        $this->variants->add($variant);
    }

    /**
     * @return Collection|Variant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }
}
