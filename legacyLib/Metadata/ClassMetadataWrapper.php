<?php

namespace Xsolve\LegacyAssociate\Metadata;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;

class ClassMetadataWrapper
{
    /**
     * @var MetadataWrapperProvider
     */
    protected $metadataWrapperProvider;

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
     * @var AssociationMetadataWrapper[]
     */
    protected $associationMetadataWrappers = [];

    /**
     * @param MetadataWrapperProvider $metadataWrapperProvider
     * @param EntityRepository        $repository
     * @param ClassMetadata           $classMetadata
     */
    public function __construct(
        MetadataWrapperProvider $metadataWrapperProvider,
        EntityRepository $repository,
        ClassMetadata $classMetadata
    ) {
        $this->metadataWrapperProvider = $metadataWrapperProvider;
        $this->repository = $repository;
        $this->classMetadata = $classMetadata;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->classMetadata->getName();
    }

    /**
     * @return string
     */
    public function getRootClassName()
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
     * @param mixed $object
     *
     * @return mixed
     */
    public function getIdentifierValueForOne($object)
    {
        $identifierValues = $this->classMetadata->getIdentifierValues($object);

        return $identifierValues[$this->getIdentifierFieldName()];
    }

    /**
     * @param array $objects
     *
     * @return array
     */
    public function getIdentifierValueForMultiple(array $objects): array
    {
        return array_map(
            function ($object) {
                return $this->getIdentifierValueForOne($object);
            },
            $objects
        );
    }

    /**
     * @param string $associationName
     *
     * @return AssociationMetadataWrapper|null
     *
     * @throws MappingException
     */
    public function getAssociationMetadataWrapper(string $associationName)
    {
        if (!array_key_exists($associationName, $this->associationMetadataWrappers)) {
            try {
                $associationMapping = $this->classMetadata->getAssociationMapping($associationName);
            } catch (MappingException $e) {
                if (0 === strpos($e->getMessage(), 'No mapping found for field ')) {
                    return null;
                }
                throw $e;
            }
            $this->associationMetadataWrappers[$associationName] = new AssociationMetadataWrapper(
                $this->metadataWrapperProvider,
                $associationMapping
            );
        }

        return $this->associationMetadataWrappers[$associationName];
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getIdentifierFieldName()
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
