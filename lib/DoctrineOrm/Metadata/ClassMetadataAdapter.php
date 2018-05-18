<?php

namespace Xsolve\Associate\DoctrineOrm\Metadata;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;

class ClassMetadataAdapter
{
    /**
     * @var MetadataAdapterProvider
     */
    protected $metadataAdapterProvider;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var string|null
     */
    protected $identifierFieldName;

    /**
     * @var AssociationMetadataAdapter[]
     */
    protected $associationMetadataAdapters = [];

    /**
     * @param MetadataAdapterProvider $metadataAdapterProvider
     * @param EntityRepository        $repository
     * @param ClassMetadata           $classMetadata
     */
    public function __construct(
        MetadataAdapterProvider $metadataAdapterProvider,
        EntityRepository $repository,
        ClassMetadata $classMetadata
    ) {
        $this->metadataAdapterProvider = $metadataAdapterProvider;
        $this->repository = $repository;
        $this->classMetadata = $classMetadata;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->classMetadata->getName();
    }

    /**
     * @return string
     */
    public function getRootClassName(): string
    {
        return $this->classMetadata->rootEntityName;
    }

    /**
     * @param string $rootAlias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(string $rootAlias): QueryBuilder
    {
        return $this->repository->createQueryBuilder($rootAlias);
    }

    /**
     * @param object $object
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getIdentifierValueForOne(object $object)
    {
        $identifierValues = $this->classMetadata->getIdentifierValues($object);

        return $identifierValues[$this->getIdentifierFieldName()];
    }

    /**
     * @param object[] $objects
     *
     * @return array
     */
    public function getIdentifierValueForMultiple(array $objects): array
    {
        return array_map(
            function (object $object) {
                return $this->getIdentifierValueForOne($object);
            },
            $objects
        );
    }

    /**
     * @param string $associationName
     *
     * @return AssociationMetadataAdapter|null
     *
     * @throws MappingException
     */
    public function getAssociationMetadataAdapter(string $associationName): ?AssociationMetadataAdapter
    {
        if (!array_key_exists($associationName, $this->associationMetadataAdapters)) {
            try {
                $associationMapping = $this->classMetadata->getAssociationMapping($associationName);
            } catch (MappingException $e) {
                if (0 === strpos($e->getMessage(), 'No mapping found for field ')) {
                    return null;
                }
                throw $e;
            }
            $this->associationMetadataAdapters[$associationName] = new AssociationMetadataAdapter(
                $this->metadataAdapterProvider,
                $associationMapping
            );
        }

        return $this->associationMetadataAdapters[$associationName];
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getIdentifierFieldName(): string
    {
        if (is_null($this->identifierFieldName)) {
            $identifierFieldNames = $this->classMetadata->getIdentifierFieldNames();

            if (1 !== count($identifierFieldNames)) {
                throw new \Exception('Composite primary keys are not supported.');
            }

            $this->identifierFieldName = reset($identifierFieldNames);
        }

        return $this->identifierFieldName;
    }
}
