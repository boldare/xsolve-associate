<?php

namespace Xsolve\LegacyAssociate\Loader;

use Xsolve\LegacyAssociate\Metadata\ClassMetadataWrapper;

class DoctrineOrmUninitializedProxiesQueryExecutor
{
    /**
     * @param array                $entities
     * @param ClassMetadataWrapper $classMetadataWrapper
     */
    public function execute(
        array $entities,
        ClassMetadataWrapper $classMetadataWrapper
    ) {
        $queryBuilder = $classMetadataWrapper->createQueryBuilder('e');
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in(
                    'e',
                    $classMetadataWrapper->getIdentifierValueForMultiple($entities)
                )
            )
            ->getQuery()
            ->execute();
    }
}
