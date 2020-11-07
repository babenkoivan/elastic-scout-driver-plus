<p align="center">
    <img width="400px" src="logo.gif">
</p>

<p align="center">
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/v/stable"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/downloads"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/license"></a>
    <a href="https://travis-ci.com/babenkoivan/elastic-scout-driver-plus"><img src="https://travis-ci.com/babenkoivan/elastic-scout-driver-plus.svg?branch=master"></a>
    <a href="https://paypal.me/babenkoi"><img src="https://img.shields.io/badge/donate-paypal-blue"></a>
    <a href="https://www.amazon.de/Amazon-de-e-Gift-Voucher-Various-Designs/dp/B07Q1JNC7R"><img src="https://img.shields.io/badge/donate-amazon-black"></a>
</p>

---

Extension for [Elastic Scout Driver](https://github.com/babenkoivan/elastic-scout-driver).

## Contents

* [Features](#features)
* [Compatibility](#compatibility)
* [Installation](#installation)
* [Usage](#usage)
    * [Generic Methods](docs/generic-methods.md)
    * [Compound Queries](docs/compound-queries.md)
    * [Full Text Queries](docs/full-text-queries.md)
    * [Joining Queries](docs/joining-queries.md)
    * [Term Queries](docs/term-queries.md)
    * [Search Results](docs/search-results.md)

## Features

Elastic Scout Driver Plus supports:

* [Search across multiple indices](docs/generic-methods.md#join)
* [Aggregations](docs/generic-methods.md#aggregate)
* [Highlighting](docs/generic-methods.md#highlight)
* [Suggesters](docs/generic-methods.md#suggest)
* [Source filtering](docs/generic-methods.md#source)
* [Field collapsing](docs/generic-methods.md#collapse)

## Compatibility

The current version of Elastic Scout Driver Plus has been tested with the following configuration:

* PHP 7.2-7.4
* Elasticsearch 7.0-7.6
* Laravel 6.x-7.x
* Laravel Scout 7.x-8.x
* Elastic Scout Driver 1.x

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-scout-driver-plus
```

**Note**, that the library doesn't work without Elastic Scout Driver. If it's not installed yet, please follow
the installation steps described [here](https://github.com/babenkoivan/elastic-scout-driver#installation). If you
are already using Elastic Scout Driver, I recommend you to update it before installing Elastic Scout Driver Plus:

```bash
composer update babenkoivan/elastic-scout-driver
```

If you want to use Elastic Scout Driver Plus with [Lumen framework](https://lumen.laravel.com/)
refer to [this guide](https://github.com/babenkoivan/elastic-scout-driver-plus/wiki/Lumen-Installation).

## Usage

Elastic Scout Driver Plus comes with a new trait, which you need to add in your model to activate advanced search functionality:

```php
class Book extends Model
{
    use Searchable;
    use CustomSearch;
}
```

This trait adds a bunch of factory methods in your model: `boolSearch()`, `matchSearch()`, `rawSearch()`, etc.
Each method creates a search request builder for the specific query type. For example, if you want to make a 
[match query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html) use `matchSearch()` method: 

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->fuzziness('AUTO')
    ->size(10)
    ->execute();
``` 

Choose factory method depending on the query type you wish to perform:
* [bool](docs/compound-queries.md#boolean) 
* [exists](docs/term-queries.md#exists) 
* [fuzzy](docs/term-queries.md#fuzzy)
* [ids](docs/term-queries.md#ids)
* [match all](docs/full-text-queries.md#match-all)
* [match none](docs/full-text-queries.md#match-none)
* [match phrase prefix](docs/full-text-queries.md#match-phrase-prefix)
* [match phrase](docs/full-text-queries.md#match-phrase)
* [match](docs/full-text-queries.md#match)
* [multi-match](docs/full-text-queries.md#multi-match)
* [nested](docs/joining-queries.md#nested)
* [prefix](docs/term-queries.md#prefix)
* [range](docs/term-queries.md#range)
* [regexp](docs/term-queries.md#regexp)
* [term](docs/term-queries.md#term)
* [terms](docs/term-queries.md#terms)
* [wildcard](docs/term-queries.md#wildcard)

If there is no method for the query type you need, you can use `rawSearch()`:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->execute();
```

It is **important to know**, that all search request builders share the same [generic methods](docs/generic-methods.md), 
which provide such basic functionality as sorting, highlighting, etc. Check the full list of available generic methods 
and the usage examples [here](docs/generic-methods.md).

Finally, refer to [this page](docs/search-results.md) to get familiar with `$searchResult` object, pagination and more.
