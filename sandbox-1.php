<?php

use Xsolve\Associate\DoctrineOrm\Association\Path\Builder\AssociationPathBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$doctrineOrmAssociationPathBuilder = new AssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
        ->aliasAs('bars')
        ->loadFull()
    ->associate('baz')
        ->aliasAs('bazs')
    ->create();

dump($doctrineOrmAssociationPath);

$doctrineOrmAssociationPathBuilder = new AssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
    ->associate('baz')
    ->create();

dump($doctrineOrmAssociationPath);
