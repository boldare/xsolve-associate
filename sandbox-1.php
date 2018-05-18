#!/usr/bin/php
<?php

use Xsolve\Associate\DoctrineOrm\Association\Path\Builder\AssociationPathBuilder;

require_once __DIR__ . '/vendor/autoload.php';

// TODO Prepare sample data (can be random at this stage).
// TODO Make some legacyTests out of this.

$doctrineOrmAssociationPathBuilder = new AssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
        ->aliasAs('bars')
        ->loadFull()
    ->associate('baz')
    ->associate('qux')
        ->aliasAs('qux')
        ->loadProxy()
    ->create();

dump($doctrineOrmAssociationPath);

$doctrineOrmAssociationPathBuilder = new AssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
    ->associate('baz')
    ->associate('qux')
    ->loadFull()
    ->create();

dump($doctrineOrmAssociationPath);
