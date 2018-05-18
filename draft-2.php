<?php

$fooIds = [$fooId1, $fooId2, $fooId3]; // Or any other iterable.
$foos = [$foo1, $foo2, $foo3]; // Or any other iterable.

// Only ids are given for entities, entity class has to be declared.
$source = new DoctrineOrmIdentifiersSource($fooIds, Foo::class);

// Entity class not declared, will be determined based on entities and metadata.
// Entities can be fully loaded or just unitialized proxies.
$source = new DoctrineOrmEntitiesSource($foos);

// Entity class declared, will be checked (or not?) and compared with metadata.
$source = new DoctrineOrmEntityiesSource($foos, Foo::class);
