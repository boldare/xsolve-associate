<?php

$sources = [$source1, $source2, $source3];
$path = ['bar', 'baz'];

$targets = $collector->getTargets($sources, $path);

$sourcesToTargetsMap = $collector->getSourcesToTargetsMap($sources, $path);
$targets1 = $sourcesToTargetsMap->get([$source1]);
$targets1And2 = $sourcesToTargetsMap->get([$source1, $source2]);

$targetIds = $collector->getTargetIds($sources, $path);

$sourcesToTargetIdsMap = $collector->getSourcesToTargetIdsMap($sources, $path);
$targetIds1 = $sourcesToTargetIdsMap->get([$source1]);
$targetIds1And2 = $sourcesToTargetIdsMap->get([$source1, $source2]);


// Need to specify strategy for final objects - if they should be fully loaded or if proxies are enough.


$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
        ->aliasAs('bars')
        ->loadFull()
    ->associate('baz')
        ->aliasAs('bazs')
    ->create();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
    ->associate('baz')
    ->create();

// Equivalent to
$doctrineOrmAssociationPath = new DoctrineOrmAssociationPath([
    (new DoctrineOrmAssociation('bar'))
        ->setAlias('bars')
        ->setLoadMode(DoctrineOrmAssociationLoad::FULL),
    (new DoctrineOrmAssociation('baz'))
        ->setLoadMode(DoctrineOrmAssociationLoad::PROXY),
]);

$doctrineOrmEntitiesSource = (new DoctrineOrmEntitiesSource([$foo1, $foo2, $foo3]))
    ->declareEntityClass(Foo::class);

$targets = $associate
    ->getAdapter('doctrine')
    ->getTargets($doctrineOrmEntitiesSource, $doctrineOrmAssociationPath)

$bazs = $targets->getAll();
