#!/usr/bin/php
<?php

use Xsolve\Associate\DoctrineOrm\Association\Path\Builder\DivergingAssociationPathBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$doctrineOrmAssociationPathBuilder = new DivergingAssociationPathBuilder();

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

$doctrineOrmAssociationPathBuilder = new DivergingAssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('bar')
    ->associate('baz')
    ->associate('qux')
        ->loadFull()
    ->create();

dump($doctrineOrmAssociationPath);

// .foo.bar
//     .baz.qux
// .qux
//
$doctrineOrmAssociationPathBuilder = new DivergingAssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->diverge()
        ->associate('foo')
        ->diverge()
            ->associate('bar')
        ->endDiverge()
        ->diverge()
            ->associate('baz')
                ->aliasAs('.foo.baz')
                ->loadProxy()
            ->associate('qux')
        ->endDiverge()
    ->endDiverge()
    ->diverge()
        ->associate('qux')
    ->endDiverge()
    ->create();

dump($doctrineOrmAssociationPath);

// .foo.bar.baz.qux
//         .qux
//
$doctrineOrmAssociationPathBuilder = new DivergingAssociationPathBuilder();

$doctrineOrmAssociationPath = $doctrineOrmAssociationPathBuilder
    ->associate('foo')
    ->associate('bar')
    ->diverge()
        ->associate('baz')
        ->associate('qux')
    ->endDiverge()
    ->diverge()
        ->associate('qux')
    ->endDiverge();

dump($doctrineOrmAssociationPath);
