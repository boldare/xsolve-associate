<?php

namespace Xsolve\AssociateTests\Functional\GenericEntity\Mock\Model;

class User
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Store[]
     */
    protected $stores;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param Store $store
     */
    public function addStore(Store $store)
    {
        $this->stores[] = $store;
    }

    /**
     * @return Store[]
     */
    public function getStores(): array
    {
        return $this->stores;
    }
}
