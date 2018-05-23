<?php

namespace Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table(name="user")
 */
class User
{
    /**
     * @var string
     *
     * @Id
     * @Column(type="string", length=64)
     */
    protected $id;

    /**
     * @var Collection|Store[]
     *
     * @OneToMany(targetEntity="Store", mappedBy="user")
     */
    protected $stores;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;

        $this->stores = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param Store[] $stores
     */
    public function setStores(array $stores)
    {
        $this->stores->clear();
        foreach ($stores as $store) {
            $this->stores->add($store);
        }
    }

    /**
     * @param Store $store
     */
    public function addStore(Store $store)
    {
        $this->stores->add($store);
    }

    /**
     * @return Collection|Store[]
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }
}
