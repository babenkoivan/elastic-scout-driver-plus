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

* [Compatibility](#compatibility)
* [Installation](#installation) 
* [Usage](#usage)

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
the installation steps described [here](https://github.com/babenkoivan/elastic-scout-driver#installation).    

## Usage

Elastic Scout Driver Plus doesn't replace or modify default `search` method provided by Laravel Scout. Instead, it
introduces a list of advanced query builders, which allow you to make complex search requests.

To get started with the builders you need to add `CustomSearch` trait to your model:

```php
class MyModel
{
    use \ElasticScoutDriverPlus\CustomSearch;
}
```

### Bool Search

Use `boolSearch` method to make a bool request:

```php
MyModel::boolSearch()
    // must clause 
    ->must('match', ['title' => 'incredible'])
    ->mustRaw(['match' => ['title' => 'incredible']])
    // must not clause
    ->mustNot('match', ['title' => 'incredible'])
    ->mustNotRaw(['match' => ['title' => 'incredible']])
    // should clause
    ->should('match', ['title' => 'incredible'])
    ->shouldRaw(['match' => ['title' => 'incredible']])
    ->minimumShouldMatch(1)
    // filter clause
    ->filter('term', ['year' => 2020])
    ->filterRaw(['term' => ['year' => 2020]])
    // soft deletes
    ->onlyTrashed()
    ->withTrashed()
    // sorting
    ->sort('year', 'asc')
    ->sortRaw([['year' => 'asc']])
    // highlighting
    ->highlight('title', ['number_of_fragments' => 3])
    ->highlightRaw(['fields' => ['title' => ['number_of_fragments' => 3]]])
    // pagination
    ->from(0)
    ->size(20)
    // suggest
    ->suggest('title_suggest', ['text' => 'incrediple', 'term' => ['field' => 'title']])
    ->suggestRaw(['title_suggest' => ['text' => 'incrediple', 'term' => ['field' => 'title']]])
    // source filtering
    ->source(['title', 'description'])
    ->sourceRaw(false)
    // field collapsing
    ->collapse('user')
    ->collapseRaw(['field' => 'user'])
    // aggregations
    ->aggregate('max_price', ['max' => ['field' => 'price']])
    ->aggregateRaw(['max_price' => ['max' => ['field' => 'price']]])
    // execute request and return array result
    ->raw()
    // execute request and return SearchResult instance (see below for more details)
    ->execute();
```

### Raw Search

Use `rawSearch` method to make a custom search request:

```php
MyModel::rawSearch()
    // raw query
    ->query(['match' => ['title' => 'incredible']])
    // sorting
    ->sort('year', 'asc')
    ->sortRaw([['year' => 'asc']])
    // highlighting
    ->highlight('title', ['number_of_fragments' => 3])
    ->highlightRaw(['fields' => ['title' => ['number_of_fragments' => 3]]])
    // pagination
    ->from(0)
    ->size(20)
    // suggest
    ->suggest('title_suggest', ['text' => 'incrediple', 'term' => ['field' => 'title']])
    ->suggestRaw(['title_suggest' => ['text' => 'incrediple', 'term' => ['field' => 'title']]])
    // source filtering
    ->source(['title', 'description'])
    ->sourceRaw(false)
    // field collapsing
    ->collapse('user')
    ->collapseRaw(['field' => 'user'])
    // aggregations
    ->aggregate('max_price', ['max' => ['field' => 'price']])
    ->aggregateRaw(['max_price' => ['max' => ['field' => 'price']]])
    // execute request and return array result
    ->raw()
    // execute request and return SearchResult instance (see below for more details)
    ->execute();
```

### Search Result

Whenever you preform a search request, `SearchResult` instance is returned: 

```php
$searchResult = MyModel::boolSearch()
    ->must('match_all')
    ->execute();
```

With `SearchResult` you can quickly get access to collection of models, documents, highlights, suggestions and aggregations:

```php
$searchResult->models();
$searchResult->documents();    
$searchResult->highlights();
$searchResult->suggestions();
$searchResult->aggregations();
```

And of course the total amount of matches:
              
```php
$searchResult->total();
```

You can also retrieve a collection of matches, which group related model, document and highlight:

```php
$matches = $searchResult->matches();
$firstMatch = $matches->first();

// model
$model = $firstMatch->model();

// document
$document = $firstMatch->document();
$documentId = $document->getId();
$documentContent = $document->getContent();

// highlight
$highlight = $firstMatch->highlight();
$highlightedSnippets = $highlight->getSnippets('title');
$rawHighlight = $highlight->getRaw();
```

**Note**, that models are lazy loaded, which means, that they will be fetched from the database with a single query and 
only when it's needed. 

### Multiple Models Search

It is possible to have multiple models in one index and search through them. In order to do so, one must make a class 
that extends the `ElasticScoutDriverPlus\Searchable\Aggregator` class and state the searchable models.

You can still optionally include the `CustomSearch` trait.

For example:

```php
namespace App\Search;

use ElasticScoutDriverPlus\CustomSearch;
use ElasticScoutDriverPlus\Searchable\Aggregator;

class Mixed extends Aggregator
{
    use CustomSearch;

    protected $models = [
        Article::class,
        Book::class,
    ];
}
```

In order to keep the index updated, boot the observer for your aggregator in your ServiceProvider like this:

```php
namespace App\Providers;

use App\Search\Mixed;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Mixed::bootSearchable();
    }
}
```
