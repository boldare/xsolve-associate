[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-associate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-associate/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/xsolve-pl/model-factory-bundle/v/stable)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![Total Downloads](https://poser.pugx.org/xsolve-pl/model-factory-bundle/downloads)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![Monthly Downloads](https://poser.pugx.org/xsolve-pl/model-factory-bundle/d/monthly)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![License](https://poser.pugx.org/xsolve-pl/model-factory-bundle/license)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)

Table of contents
=================

  * [Introduction](#introduction)
  * [License](#license)

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
