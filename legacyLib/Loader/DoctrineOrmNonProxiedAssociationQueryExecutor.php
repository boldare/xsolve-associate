<?php

namespace Xsolve\LegacyAssociate\Loader;

use Xsolve\LegacyAssociate\Metadata\AssociationMetadataWrapper;

class DoctrineOrmNonProxiedAssociationQueryExecutor
{
    /**
     * @param array                      $entities
     * @param AssociationMetadataWrapper $associationMetadataWrapper
     */
    public function execute(
        array $entities,
        AssociationMetadataWrapper $associationMetadataWrapper
    ) {
        $sourceClassMetadataWrapper = $associationMetadataWrapper->getSourceClassMetadataWrapper();

        $queryBuilder = $sourceClassMetadataWrapper->createQueryBuilder('s');
        $queryBuilder
            ->select(
                sprintf('PARTIAL s.{%s}', $sourceClassMetadataWrapper->getIdentifierFieldName()),
                't'
            )
            ->leftJoin(
                sprintf('s.%s', $associationMetadataWrapper->getName()),
                't'
            )
            ->andWhere(
                $queryBuilder->expr()->in(
                    's',
                    $sourceClassMetadataWrapper->getIdentifierValueForMultiple($entities)
                )
            )
            ->getQuery()
            ->execute();
    }
}
