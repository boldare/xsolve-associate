<?php

namespace Xsolve\Associate\DoctrineOrm\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class MetadataAdapterProvider
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var (ClassMetadataAdapter|null)[]
     */
    protected $classMetadataAdapters = [];

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $objects
     *
     * @return ClassMetadataAdapter
     *
     * @throws \Exception
     */
    public function getClassMetadataAdapterForEntities(array $objects): ClassMetadataAdapter
    {
        $className = $this->getEntityClassForEntities($objects);
        $classMetadataAdapter = $this->getClassMetadataAdapterByClassName($className);
        if (!$classMetadataAdapter instanceof ClassMetadataAdapter) {
            throw new \Exception();
        }

        return $classMetadataAdapter;
    }

    /**
     * @param array $objects
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getEntityClassForEntities(array $objects): string
    {
        if (!$objects) {
            throw new \Exception();
        }

        $firstObject = array_shift($objects);
        $classMetadataAdapter = $this->getClassMetadataAdapterByClassName(get_class($firstObject));
        if (!$classMetadataAdapter instanceof ClassMetadataAdapter) {
            throw new \Exception();
        }
        $commonClassName = $classMetadataAdapter->getClassName();
        $rootClassNameUsed = false;

        foreach ($objects as $object) {
            if (is_a($object, $commonClassName, true)) {
                continue;
            }
            if ($rootClassNameUsed) {
                throw new \Exception();
            }
            $commonClassName = $classMetadataAdapter->getRootClassName();
            if (!is_a($object, $commonClassName, true)) {
                throw new \Exception();
            }
        }

        return $commonClassName;
    }

    /**
     * @param string $className
     *
     * @return ClassMetadataAdapter|null
     *
     * @throws \Exception
     */
    public function getClassMetadataAdapterByClassName(string $className): ?ClassMetadataAdapter
    {
        if (!array_key_exists($className, $this->classMetadataAdapters)) {
            $this->initializeClassMetadataAdapterByClassName($className);
        }

        return $this->classMetadataAdapters[$className];
    }

    /**
     * @param string $className
     *
     * @throws \Exception
     */
    protected function initializeClassMetadataAdapterByClassName(string $className): void
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        if (!$classMetadata instanceof ClassMetadata) {
            $this->classMetadataAdapters[$className] = null;

            return;
        }

        $entityRepository = $this->entityManager->getRepository($className);
        if (!$entityRepository instanceof EntityRepository) {
            throw new \Exception();
        }
        $this->classMetadataAdapters[$className] = new ClassMetadataAdapter($this, $entityRepository, $classMetadata);
    }
}
