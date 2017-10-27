[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-associate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-associate/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/xsolve-pl/associate/v/stable)](https://packagist.org/packages/xsolve-pl/associate)
[![Total Downloads](https://poser.pugx.org/xsolve-pl/associate/downloads)](https://packagist.org/packages/xsolve-pl/associate)
[![Monthly Downloads](https://poser.pugx.org/xsolve-pl/associate/d/monthly)](https://packagist.org/packages/xsolve-pl/associate)
[![License](https://poser.pugx.org/xsolve-pl/associate/license)](https://packagist.org/packages/xsolve-pl/associate)

Table of contents
=================

  * [Introduction](#introduction)
  * [License](#license)
  * [Getting started](#getting-started)
  * [Usage examples](#usage-examples)
    * [Collecting associated objects and values](#collecting-associated-objects-and-values)
    * [Efficiently load associated entities and solve N+1 queries problem](#efficiently-load-associated-entities-and-solve-N+1-queries-problem)
    * [Defer loading entities to load them in bulk](#defer-loading-entities-to-load-them-in-bulk)

Introduction
============

This library allows to collect objects and values through associations
and provides some entity fetching optimizations for Doctrine ORM to
address N+1 queries problem.

It can play nicely with `Deferred` implementation from `webonyx/graphql-php`
allowing to significantly reduce number of database queries.

License
=======

This bundle is under the MIT license. See the complete license in `LICENSE` file.

Getting started
===============

Include this bundle in your project using Composer as follows
(assuming it is installed globally):

```bash
$ composer require xsolve-pl/associate
```

For more information on Composer see its
[Introduction](https://getcomposer.org/doc/00-intro.md).

To get the basic collector you may use the facade provided with the library:

```php
<?php
$facade = new \Xsolve\Associate\Facade();
$basicCollector = $facade->getBasicCollector();
```

If you want to use collector dedicated for Doctrine ORM, provide appropriate
entity manager when instantiating the facade and retrieve dedicated collector:

```php
<?php
$facade = new \Xsolve\Associate\Facade($entityManager);
$doctrineOrmCollector = $facade->getDoctrineOrmCollector();
```

You are also free to compose your own collectors using building blocks provided
by this library, as well as replacing the facade with some DI container
configuration suitable for you framework of choice.

That's all - now you're ready to go!

Usage examples
==============

Collecting associated objects and values
----------------------------------------

First functionality provided with this bundle allows to retrieve all objects
that can be reached via specified associations starting from some base objects.

Let's assume we have following classes defined:

```php
<?php

class Foo
{
    /**
     * @var Bar
     */
    protected $bar;

    /**
     * @param Bar $bar
     */
    public function __construct(Bar $bar)
    {
        $this->bar = bar;
    }

    /**
     * @return Bar
     */
    public function getBar(): Bar
    {
        return $this->bar;
    }
}

class Bar
{
    /**
     * @var Baz[]
     */
    public $bazs;

    /**
     * @param Baz[] $bazs
     */
    public function __construct(array $bazs)
    {
        $this->bazs = bazs;
    }
}

class Baz
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return explode(' ', $this->text);
    }

    /**
     * @return array
     */
    public function getTextStats(): array
    {
        return ['wordCount' => count($this->getWords())];
    }
}
```

Now let's assume we have some instances of `Foo` class in `$foos` array
as well as some associated objects:

```php
<?php
$foos = [
    $foo1 = new Foo(
        $bar1 = new Bar([
            $baz1 = new Baz('lorem ipsum'),
            $baz2 = new Baz('dolor'),
        ])
    ),
    $foo2 = new Foo(
        $bar2 = new Bar([
            $baz1,
            $baz3 = new Baz('sit amet malef'),
            $baz4 = new Baz('dolor sit'),
        ])
    ),
];
```


We would like to collect all `Bar` instances that they are associated with.
It is as easy as writing:

```php
<?php
$bars = $basicCollector->collect($foos, ['bar']);
// $bars ~= [$bar1, $bar2]; - order is not guaranteed.
```

**Important!** Note that the order of `$bars` is not guaranteed.
It is so because internally instance of `\SplObjectStorage` is used
to assert the uniqueness of collected objects.

We can go further with that and collect objects that are two associations
away by doing:

```php
<?php
$bazs = $basicCollector->collector($foos, ['bar', 'bazs']);
// $bazs ~= [$baz1, $baz2, $baz3, $baz4]; - order is not guaranteed.
```

Note that only one reference `$baz1` will be included as it will be detected
that the same object was associated view `$bar1` and `$bar2`.

It is also possible to collect scalar values, but in this case uniqueness will not
be imposed on them:

```php
<?php
$texts = $basicCollector->collector($foos, ['bar', 'bazs', 'text']);
// $texts ~= ['lorem ipsum', 'dolor', 'sit amet malef', 'dolor sit']; - order is not guaranteed.
```

If given association yields an array with sequential numeric indices
starting with `0` it is automatically assumed that it is a collection
of objects or values (i.e. that association links given object to many
objects). Therefore it is possible to write:

```php
<?php
$words = $basicCollector->collector($foos, ['bar', 'bazs', 'words']);
// $words ~= ['lorem', 'ipsum','dolor', 'sit', 'amet', 'malef', 'dolor', 'sit']; - order is not guaranteed.
```

This time `dolor` is present twice as it is a scalar value and uniqueness was not imposed.

However if an array is associative we can also go deeper into it
when collecting values:

```php
<?php
$wordCounts = $basicCollector->collector($foos, ['bar', 'bazs', 'textStats', 'wordCount']);
// $wordCounts ~= [2, 1, 3, 2]; - order is not guaranteed.
```

Internally [`symfony/property-access`](https://packagist.org/packages/symfony/property-access)
is used to follow associations so they may be accessible in different ways. Please consult
[its documentation](https://symfony.com/doc/current/components/property_access.html)
for possible options.

Efficiently load associated entities and solve N+1 queries problem
------------------------------------------------------------------

TODO

Defer loading entities to load them in bulk
-------------------------------------------

If you're working on a project using Doctrine ORM and providing GraphQL API
then this library can play nicely with `Deferred` class provided by
[webonyx/graphql-php](https://packagist.org/packages/webonyx/graphql-php).
You can read more about the general idea behind this approach at
[Solving N+1 Problem](http://webonyx.github.io/graphql-php/data-fetching/#solving-n1-problem)
section of its documentation.

Let's assume we need to implement `resolve` function that will return `Variant` instances for
`Product` instance. Basic implementation could look as follows:

```php
<?php
$resolve = function(Product $product) {
    return $product->getVariants();
};
```

While this will work just fine, it will issue a separate `SELECT` query for each instance
of `Product` class (unless this association has fetch mode set to `EAGER`).
Hence if we want to list 100 products, we would end up with 101 database queries.

To alleviate this problem and to load these objects efficiently we can use instance of `BufferedCollector`
like this:

```php
<?php
$facade = new \Xsolve\Associate\Facade($entityManager);
$bufferedCollector = $facade->getBufferedCollector();

$resolve = function(Product $product) use ($bufferedCollector) {
    $bufferedCollectClosure = $bufferedCollector->createCollectClosure([$product], ['variants']);

    return new \GraphQL\Deferred(function() use ($bufferedCollectClosure) {
        return $bufferedCollectClosure();
    });
};
```

Et voilà - it is that simple! What `BufferedCollector` will do it will accumulate
all collect parameters it is provided with. When GraphQL library attempts to resolve
`Deferred` that was returned in our `resolve` function the collector will group all stored
parameters by same base object class and association path and will load all of them in
a single batch, issuing only 1 `SELECT` query. Hence we will end up with 2 queries
instead of 101.
