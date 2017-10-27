[![Build Status](https://travis-ci.org/xsolve-pl/xsolve-associate.svg?branch=master)](https://travis-ci.org/xsolve-pl/xsolve-associate)
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

It can play especially nicely with `Deferred` implementation
from `webonyx/graphql-php` allowing to significantly reduce number
of database queries.

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

You can also compose your own collectors using building blocks provided
by this library. It's also possible to replace the facade provided
and replace it with some configuration for DI container your framework uses.

That's all - now you're ready to go!

Usage examples
==============

Collecting associated objects and values
----------------------------------------

First functionality provided by this library it to allow to retrieve all objects
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

Now we'd like to collect all `Bar` instances that `$foos` are associated with.
It's as easy as this:

```php
<?php
$bars = $basicCollector->collect($foos, ['bar']);
// $bars ~= [$bar1, $bar2]; - order is not guaranteed.
```

**Important!** Note that the order of `$bars` is not guaranteed.
It's so because `\SplObjectStorage` is used internally to assert the uniqueness
of collected objects.

We can go further with that and collect objects that are two associations
away from `$foos` by doing:

```php
<?php
$bazs = $basicCollector->collector($foos, ['bar', 'bazs']);
// $bazs ~= [$baz1, $baz2, $baz3, $baz4]; - order is not guaranteed.
```

Note that only one reference `$baz1` will be included as it will be detected
that the same object was associated view `$bar1` and `$bar2`.

It is also possible to collect scalar values but in this case uniqueness will not
be imposed on them:

```php
<?php
$texts = $basicCollector->collector($foos, ['bar', 'bazs', 'text']);
// $texts ~= ['lorem ipsum', 'dolor', 'sit amet malef', 'dolor sit']; - order is not guaranteed.
```

If given association yields an array with sequential numeric indices
starting with `0` it is automatically assumed that it is a collection
of objects or scalars (i.e. that association links given object to many
objects or scalars). Therefore it's possible to write:

```php
<?php
$words = $basicCollector->collector($foos, ['bar', 'bazs', 'words']);
// $words ~= [
//     'lorem', 'ipsum','dolor', 'sit',
//     'amet', 'malef', 'dolor', 'sit',
// ]; - order is not guaranteed.
```

This time `dolor` is present twice as it is a scalar value and uniqueness
was not imposed.

However if an array is associative we can go even deeper when collecting values:

```php
<?php
$wordCounts = $basicCollector->collector($foos, ['bar', 'bazs', 'textStats', 'wordCount']);
// $wordCounts ~= [2, 1, 3, 2]; - order is not guaranteed.
```

Internally [`symfony/property-access`](https://packagist.org/packages/symfony/property-access)
is used to follow associations so they may be accessible in different ways -
for instance as a public property or via a getter method.
Please consult
[its documentation](https://symfony.com/doc/current/components/property_access.html)
for possible options.

Efficiently load associated entities and solve N+1 queries problem
------------------------------------------------------------------

Let's assume that we're building an e-commerce website using
[doctrine/orm](https://packagist.org/packages/doctrine/orm)
for persistence. One of the things we can run into is N+1 queries problem
which occurs when we fetch some entities from database and then attempt
to traverse their associations via getters.

For example we can have some products. Each of them has some variants which in turn
have a property storing available inventory quantity. Now we would like to find out
which products are available for sale and we already have `Product` instances loaded
from database (e.g. after taking into account some filters that user applied).
We could use code like this:

```php
<?php
$availableProducts = array_filter(
    $products,
    function(Product $product) {
        foreach ($product->getVariants() as $variant) {
            if ($variant->getInventoryQuantity() > 0) {
                return true;
            }
        }

        return false;
    }
);
```

While this will work perfectly fine it will incur one `SELECT` query each time we call
`getVariants` method on given `Variant` instance for the first time. Hence if we want
to check availability for 100 products we would end up with 101 database queries executed.

You can find out more about this problem
at [5 Doctrine ORM Performance Traps You Should Avoid](https://tideways.io/profiler/blog/5-doctrine-orm-performance-traps-you-should-avoid)
written by [Benjamin Eberlei](https://github.com/beberlei) -
see section titled in section *Lazy-Loading and N+1 Queries*.
Four ways to address this problem are pointed out there.

Eager loading (solution 3) can be the simplest way to go in some cases
but in many cases we will find it too rigid. It is possible that we don't want specific
association to be loaded always but just in some cases.

Other solutions are more flexible, like using dedicated DQL query (solution 1)
or triggering eager loading of entities after collecting their identifiers (solution 2).

These solutions would however result in clunky code and they have to be adjusted
depending on whether given association is of *-to-one* or *-to-many* type
and whether entities that are already initialized are on the inverse or the owning
side of the association. Also some minor optimizations can be applied
if some `\Doctrine\Common\Persistence\Proxy` instances
or `\Doctrine\ORM\PersistentCollection` instances are already initialized
and hence can be skipped.

This library tries to do exactly what is proposed in solutions 1 and 2
but in a clean and encapsulated manner. Thanks to it loading associated entities
is simple and can be applied easily. In the example above it would be only required
to precede previously given code with:

```php
<?php
$facade = new \Xsolve\Associate\Facade($entityManager);
$doctrineOrmCollector = $facade->getDoctrineOrmCollector();
$doctrineOrmCollector->collect($products, ['variants']);
```

After executing this snippet all variants for given products will be loaded
with a single `SELECT` query and calling `getVariants` will not result
in any additional queries.

If the number of products or associated entities is high they'll be split
in chunks and associations for each chunk will be loaded separately. Chunk size is
set by default to `1000` but you are free to alter it
or set it to `null` to disable chunking.

Also property values can be collected this way. If each variant has a property containing
its price and we would like to collect prices of all variants of all given products
we could execute following code:

```php
<?php
$facade = new \Xsolve\Associate\Facade($entityManager);
$doctrineOrmCollector = $facade->getDoctrineOrmCollector();
$prices = $doctrineOrmCollector->collect($products, ['variants', 'price']);
```

It's as simple as that!

**Important!** You won't be able to reduce the number of queries for one-to-one
associations starting from inverse side - Doctrine ORM loads them by default issuing
a separate `SELECT` for each entity. You may consider changing such association to
one-to-many (and use collector afterwards) or using embeddable if possible (in which case
embedded entities will be loaded with the same query that loads entities that contain them).

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

But using this approach we would again end up with N+1 queries executed against our database.
To alleviate this problem and to load these objects efficiently we can use instance of
`BufferedCollector` like this:

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

Et voilà! What `BufferedCollector` will do it will accumulate all collect jobs
while query result is build width first. When GraphQL library attempts to resolve
`Deferred` that was returned in our `resolve` function the collector will group all similar
jobs stored before (comparing base object class and association path) and will load
all of them in a single batch, issuing only 1 `SELECT` query
(or 1 query for chunk if the number of base entities is high as mentioned above).
Hence we will end up with 2 queries instead of 101.
