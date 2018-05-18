<?php

namespace Xsolve\LegacyAssociateTests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\DataProvider;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Category;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Product;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Store;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\User;
use Xsolve\LegacyAssociateTests\Functional\DoctrineOrm\Mock\Entity\Variant;

class DoctrineOrmHelper
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function setupEntityManager()
    {
        $entityPaths = [
            __DIR__ . '/DoctrineOrm/Mock/Entity',
        ];

        $isDevMode = false;

        $databaseOptions = [
            'driver' => 'pdo_sqlite',
            'dbname' => 'sifter_test', // TODO Move to some config file.
            'user' => 'root', // TODO Move to some config file.
            'password' => '', // TODO Move to some config file.
            'memory' => true, // TODO Move to some config file.
        ];

        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($entityPaths, $isDevMode);
        $this->entityManager = EntityManager::create($databaseOptions, $metadataConfiguration);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            $this->setupEntityManager();
        }

        return $this->entityManager;
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function createSchema()
    {
        $this->getEntityManager();

        $entityClassMetadatas = [];
        foreach ($this->getEntityClassNames() as $entityClassName) {
            $entityClassMetadatas[] = $this->entityManager->getClassMetadata($entityClassName);
        }

        $tool = new SchemaTool($this->entityManager);
        $tool->createSchema($entityClassMetadatas);
    }

    public function loadData()
    {
        $this->getEntityManager();

        $dataProvider = new DataProvider();
        foreach ($dataProvider->getAll() as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @return string[]
     */
    protected function getEntityClassNames(): array
    {
        return [
            Category::class,
            User::class,
            Store::class,
            Product::class,
            Variant::class,
        ];
    }
}
