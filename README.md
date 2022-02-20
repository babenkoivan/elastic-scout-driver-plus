<p align="center">
    <img width="400px" src="logo.gif">
</p>

<p align="center">
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/v/stable"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/downloads"></a>
    <a href="https://packagist.org/packages/babenkoivan/elastic-scout-driver-plus"><img src="https://poser.pugx.org/babenkoivan/elastic-scout-driver-plus/license"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver-plus/actions?query=workflow%3ATests"><img src="https://github.com/babenkoivan/elastic-scout-driver-plus/workflows/Tests/badge.svg"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver-plus/actions?query=workflow%3A%22Code+style%22"><img src="https://github.com/babenkoivan/elastic-scout-driver-plus/workflows/Code%20style/badge.svg"></a>
    <a href="https://github.com/babenkoivan/elastic-scout-driver-plus/actions?query=workflow%3A%22Static+analysis%22"><img src="https://github.com/babenkoivan/elastic-scout-driver-plus/workflows/Static%20analysis/badge.svg"></a>
    <a href="https://paypal.me/babenkoi"><img src="https://img.shields.io/badge/donate-paypal-blue"></a>
</p>

<p align="center">
    <a href="https://ko-fi.com/ivanbabenko" target="_blank"><img src="https://ko-fi.com/img/githubbutton_sm.svg" alt="Support the project!"></a>
</p>

---

Extension for [Elastic Scout Driver](https://github.com/babenkoivan/elastic-scout-driver).

## Contents

* [Features](#features)
* [Compatibility](#compatibility)
* [Installation](#installation)
* [Usage](#usage)
  * [Query](#query)
  * [Search parameters](#search-parameters)
  * [Search results](#search-results)
  * [Custom routing](#custom-routing)
  * [Eager loading relations](#eager-loading-relations)

## Features

Elastic Scout Driver Plus supports:

* [Search across multiple indices](docs/available-methods.md#join)
* [Aggregations](docs/available-methods.md#aggregate)
* [Highlighting](docs/available-methods.md#highlight)
* [Suggesters](docs/available-methods.md#suggest)
* [Source filtering](docs/available-methods.md#source)
* [Field collapsing](docs/available-methods.md#collapse)
* [Custom routing](#custom-routing)

## Compatibility

The current version of Elastic Scout Driver Plus has been tested with the following configuration:

* PHP 7.3-8.0
* Elasticsearch 7.x
* Laravel 6.x-8.x
* Laravel Scout 7.x-9.x
* Elastic Scout Driver 2.x

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-scout-driver-plus
```

**Note** that this library doesn't work without Elastic Scout Driver. If it's not installed yet, please follow
the installation steps described [here](https://github.com/babenkoivan/elastic-scout-driver#installation). If you
already use Elastic Scout Driver, I recommend you to update it before installing Elastic Scout Driver Plus:

```bash
composer update babenkoivan/elastic-scout-driver
```

After installing the libraries, you need to add `ElasticScoutDriverPlus\Searchable` trait to your models. In case 
some models already use the standard `Laravel\Scout\Searchable` trait, you should replace it with the one provided by 
Elastic Scout Driver Plus.

If you want to use Elastic Scout Driver Plus with [Lumen framework](https://lumen.laravel.com/)
refer to [this guide](https://github.com/babenkoivan/elastic-scout-driver-plus/wiki/Lumen-Installation).

## Usage

### Query

Before you begin searching a model, you should define a query. You can either use a query builder or describe the query
with an array:

```php
use ElasticScoutDriverPlus\Support\Query;

// using a query builder
$query = Query::match()
    ->field('title')
    ->query('My book')
    ->fuzziness('AUTO');

// using a raw query
$query = [
    'match' => [
        'title' => [
            'query' => 'My book',
            'fuzziness' => 'AUTO'
        ] 
    ]
];
```

Each method of `ElasticScoutDriverPlus\Support\Query` factory creates a query builder for the respective type. 
Available methods are listed below:

* [bool](docs/compound-queries.md#boolean)
* [exists](docs/term-queries.md#exists)
* [fuzzy](docs/term-queries.md#fuzzy)
* [geoDistance](docs/geo-queries.md#geo-distance)
* [ids](docs/term-queries.md#ids)
* [matchAll](docs/full-text-queries.md#match-all)
* [matchNone](docs/full-text-queries.md#match-none)
* [matchPhrasePrefix](docs/full-text-queries.md#match-phrase-prefix)
* [matchPhrase](docs/full-text-queries.md#match-phrase)
* [match](docs/full-text-queries.md#match)
* [multiMatch](docs/full-text-queries.md#multi-match)
* [nested](docs/joining-queries.md#nested)
* [prefix](docs/term-queries.md#prefix)
* [range](docs/term-queries.md#range)
* [regexp](docs/term-queries.md#regexp)
* [term](docs/term-queries.md#term)
* [terms](docs/term-queries.md#terms)
* [wildcard](docs/term-queries.md#wildcard)

### Search Parameters

When the query is defined, you can begin new search with `searchQuery` method:

```php
$builder = Book::searchQuery($query);
```

You can then chain other parameters to make your search request more precise:

```php
$builder = Book::searchQuery($query)
    ->size(2)
    ->sort('price', 'asc');
```

The builder supports various search parameters and provides a number of useful helpers: 

* [aggregate](docs/available-methods.md#aggregate)
* [boostIndex](docs/available-methods.md#boostindex)
* [collapse](docs/available-methods.md#collapse)
* [from](docs/available-methods.md#from)
* [highlight](docs/available-methods.md#highlight)
* [join](docs/available-methods.md#join)
* [load](docs/available-methods.md#load)
* [minScore](docs/available-methods.md#minscore)
* [postFilter](docs/available-methods.md#postfilter)
* [size](docs/available-methods.md#size)
* [sort](docs/available-methods.md#sort)
* [refineModels](docs/available-methods.md#refinemodels)
* [rescore](docs/available-methods.md#rescore)
* [refineModels](docs/available-methods.md#refinemodels)
* [source](docs/available-methods.md#source)
* [suggest](docs/available-methods.md#suggest)
* [trackScores](docs/available-methods.md#trackscores)
* [trackTotalHits](docs/available-methods.md#tracktotalhits)
* [when](docs/available-methods.md#when)

### Search Results

You can retrieve search results by chaining the `execute` method onto the builder:

```php
$searchResult = Book::searchQuery($query)->execute();
```

`$searchResult` provides easy access to matching hits, models, documents, etc.:

```php
$hits = $searchResult->hits();
$models = $searchResult->models();
$documents = $searchResult->documents();
$highlights = $searchResult->highlights();
```

You can get more familiar with the `$searchResult` object and learn how to paginate the search results on [this page](docs/search-results.md).  

### Custom Routing

If you want to use a [custom shard routing](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-routing-field.html)
for your model, override the `shardRouting` method:

```php
class Book extends Model
{
    use ElasticScoutDriverPlus\Searchable;
    
    public function shardRouting()
    {
        return $this->user->id;
    }
}
```

Custom routing is automatically applied to all index and delete operations.

### Eager Loading Relations

Sometimes you need to index your model with related data:

```php
class Book extends Model
{
    use ElasticScoutDriverPlus\Searchable;
    
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'price' => $this->price,
            'author' => $this->author->only(['name', 'phone_number']),
        ];
    }
}
```

You can improve the performance of bulk operations by overriding the `searchableWith` method:

```php
class Book extends Model
{
    use ElasticScoutDriverPlus\Searchable;
    
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'price' => $this->price,
            'author' => $this->author->only(['name', 'phone_number']),
        ];
    }
    
    public function searchableWith()
    {
        return ['author'];
    }
}
```

In case you are looking for a way to preload relations for models matching a search query, check the builder's
`load` method [documentation](docs/available-methods.md#load).
