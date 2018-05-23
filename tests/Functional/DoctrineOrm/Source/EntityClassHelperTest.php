<?php

namespace Xsolve\AssociateTests\Functional\DoctrineOrm\Source;

use PHPUnit\Framework\TestCase;
use Xsolve\Associate\DoctrineOrm\Metadata\MetadataAdapterProvider;
use Xsolve\Associate\DoctrineOrm\Source\EntityClassHelper;
use Xsolve\Associate\DoctrineOrm\Source\EntitySource;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\DataProvider;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\DataProviderInterface;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\DoctrineOrmHelper;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\Entity\Product;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\Entity\Store;
use Xsolve\AssociateTests\Functional\DoctrineOrm\Mock\Entity\Variant;

class EntityClassHelperTest extends TestCase
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @param object[] $entities
     * @param string   $expectedClassName
     *
     * @dataProvider dataSetEntityClass
     */
    public function testSetEntityClass(array $entities, string $expectedClassName): void
    {
        $doctrineOrmHelper = new DoctrineOrmHelper();
        $this->entityManager = $doctrineOrmHelper->getEntityManager();

        $metadataAdapterProvider = new MetadataAdapterProvider($this->entityManager);
        $entityClassHelper = new EntityClassHelper($metadataAdapterProvider);

        $entitySource = new EntitySource($entities);
        $entityClassHelper->supplementEntitySource($entitySource);

        $this->assertEquals($expectedClassName, $entitySource->getEntityClass());

        $this->assertContains($expectedClassName, [
            $entitySource->getClassMetadataAdapter()->getClassName(),
            $entitySource->getClassMetadataAdapter()->getRootClassName(),
        ]);
    }

    /**
     * @return array
     */
    public function dataSetEntityClass(): array
    {
        $this->dataProvider = new DataProvider();

        return [
            [
                $this->dataProvider->getProducts(['s1p1', 's1p3', 's3p1']),
                Product::class,
            ],
            [
                $this->dataProvider->getStores(['s1', 's2']),
                Store::class,
            ],
            [
                $this->dataProvider->getVariants(['s3p1v1']),
                Variant::class,
            ],
        ];
    }

    /**
     * @param object[] $entities
     *
     * @dataProvider dataSetEntityClassIfNoCommonClassName
     * @expectedException \Exception
     */
    public function testSetEntityClassIfNoCommonClassName(array $entities): void
    {
        $doctrineOrmHelper = new DoctrineOrmHelper();
        $this->entityManager = $doctrineOrmHelper->getEntityManager();

        $metadataAdapterProvider = new MetadataAdapterProvider($this->entityManager);
        $entityClassHelper = new EntityClassHelper($metadataAdapterProvider);

        $entitySource = new EntitySource($entities);
        $entityClassHelper->supplementEntitySource($entitySource);
    }

    /**
     * @return array
     */
    public function dataSetEntityClassIfNoCommonClassName(): array
    {
        $this->dataProvider = new DataProvider();

        return [
            [
                [
                    $this->dataProvider->getProducts(['s1p1'])[0],
                    $this->dataProvider->getStores(['s2'])[0],
                    $this->dataProvider->getProducts(['s3p1'])[0],
                ],
            ],
            [
                [
                    $this->dataProvider->getStores(['s1'])[0],
                    $this->dataProvider->getStores(['s2'])[0],
                    $this->dataProvider->getProducts(['s1p3'])[0],
                ],
            ],
            [
                [
                    $this->dataProvider->getVariants(['s3p1v1'])[0],
                    $this->dataProvider->getStores(['s1'])[0],
                    $this->dataProvider->getProducts(['s2p1'])[0],
                ],
            ],
            [
                [
                    new \stdClass(),
                    new \stdClass(),
                ],
            ],
        ];
    }
}
