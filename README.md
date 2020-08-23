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
* [Search Request Builder](#search-request-builder)
    * [Generic Methods](#generic-methods)
    * [Query Specific Methods](#query-specific-methods)
* [Search Result](#search-result)

## Features

Elastic Scout Driver Plus supports:

* [Search across multiple indices](#join)
* [Aggregations](#aggregate)
* [Highlighting](#highlight)
* [Suggesters](#suggest)
* [Source filtering](#source)
* [Field collapsing](#collapse)

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

If you want to use Elastic Scout Driver Plus with [Lumen framework](https://lumen.laravel.com/)
read [this guide](https://github.com/babenkoivan/elastic-scout-driver-plus/wiki/Lumen-Installation).

## Usage

Elastic Scout Driver Plus comes with a flexible, easy to use search request builder. 
Some of its methods are generic and can be used in any search request. Others are depend on the query type.

To get started with the builder, you need to add `ElasticScoutDriverPlus\CustomSearch` trait in your model:

```php
class Book extends Model
{
    use Searchable;
    use CustomSearch;
}
```

This trait adds a bunch of factory methods in your model: `boolSearch()`, `rawSearch()`, etc. 
Each method creates a new builder with query type specific methods.

Let’s have a look at the raw search example:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'My book']])
    ->size(1)
    ->execute();
``` 

In the example above we asked Elasticsearch to find exactly one book with `My book` title. 
Of course, this is a very basic query, you can do much more with Elastic Scout Driver Plus. 
Check [the available builder methods](#search-request-builder) below or jump straight to 
[the search result overview](#search-result) if you want to know how to access matching models, documents, highlights, etc.

## Search Request Builder

The search request builder provides a convenient, fluent interface to construct and execute Elasticsearch requests. 
The available methods are listed below.

### Generic Methods

Generic methods are query type agnostic, which means you can use them in any search request no matter of its type. 
For the convenience, most of the examples below use the raw search, but you are free to use generic methods with any 
search: bool, raw, etc.

Available methods:

* [aggregate](#aggregate)
* [collapse](#collapse)
* [from](#from)
* [highlight](#highlight)
* [join](#join)
* [postFilter](#postfilter)
* [size](#size)
* [sort](#sort)
* [source](#source)
* [suggest](#suggest)

#### aggregate

This method can be used to [aggregate data](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html) 
based on a search query;

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->aggregate('max_price', [
        'max' => [
            'field' => 'price',
        ],
	])
    ->execute();
```

You can also use `aggregateRaw` for more flexibility:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->aggregateRaw([
        'max_price' => [
            'max' => [
                'field' => 'price',
            ],
        ],
    ])
    ->execute();
```

You can retrieve the aggregated data from the search result as follows:

```php
$aggregations = $searchResult->aggregations();
$maxPrice = $aggregations->get('max_price');
```

#### collapse

This method allows to [collapse](https://www.elastic.co/guide/en/elasticsearch/reference/current/collapse-search-results.html) 
search results based on field values:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->collapse('author_id')
    ->sort('published', 'desc')
    ->execute();
```

There is also `collapseRaw` method in your disposal:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->collapseRaw(['field' => 'author_id'])
    ->sort('price', 'asc')
    ->execute();
```

#### from

`from` method defines [the starting result offset](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->from(5)
    ->execute();
```

#### highlight

This method allows you to get [highlighted snippets](https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html#highlighting)
from one or more fields in your search results:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->highlight('title')
    ->execute();
```

Use `highlightRaw` method if you need more control:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->highlightRaw(['fields' => ['title' => ['number_of_fragments' => 3]]])
    ->execute();
```

Use `highlights` method to retrieve highlights from the search result:

```php
$highlights = $searchResult->highlights();
```

You can also get a highlight for [every respective match](#matches):

```php
$matches = $searchResult->matches();
$highlight = $matches->first()->highlight();
```

The highlighted snippets can be retrieved as follows:

```php
$snippets = $highlight->getSnippets('title');
```

If you would rather prefer an array representation of the highlight, use `getRaw` method:

```php
$raw = $highlight->getRaw();
```

#### join

This method enables [multi indices](https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-index.html#multi-index)
search:

```php
$searchResult = Author::boolSearch()
    ->join(Book::class)
    ->should('match', ['name' => 'John'])
    ->should('match', ['title' => 'The Book'])
    ->minimumShouldMatch(1)
    ->execute();
```

In the example above, we search for an author with name `John` or a book with title `The Book` in two different indices. 
It doesn’t matter if we start the query from `Book` or `Author` model. Remember though, that the result model collection 
includes both types in this case:

```php
// every model is either Author or Book
$models = $searchResult->models();
```

#### postFilter

`postFilter` is used to [filter search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#post-filter):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->postFilter('term', ['published' => '2020-06-07'])
    ->execute();
``` 

You can also use `postFilterRaw` method as follows:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->postFilterRaw(['term' => ['published' => '2020-06-07']])
    ->execute();
```

#### size

`size` method [limits the number of results to return](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->size(2)
    ->execute();
```

#### sort

This method [sorts](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html) the results:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->sort('price', 'asc')
    ->execute();
```

In case, you need more advanced sorting algorithm use `sortRaw`:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->sortRaw(['price' => 'asc'])
    ->execute();
```

#### source

This method allows you to [select what document fields of the source are returned](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-fields.html#source-filtering):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->source(['title', 'description'])
    ->execute();
```

`sourceRaw` allows you to use a single wildcard pattern, an array of fields or a boolean value in case you want to 
exclude document source from the result:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->sourceRaw(false)
    ->execute();
```

#### suggest

This method can be used to [get similar looking terms](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#search-suggesters)
based on a provided text:

```php
$searchResult = Book::rawSearch()
    ->query(['match_none' => new \stdClass()])
    ->suggest('title_suggest', ['text' => 'book', 'term' => ['field' => 'title']])
    ->execute();
```

The same query with `suggestRaw` method:

```php
$searchResult = Book::rawSearch()
    ->query(['match_none' => new \stdClass()])
    ->suggest(['title_suggest' => ['text' => 'book', 'term' => ['field' => 'title']]])
    ->execute();
```

When the feature is used, the search result is populated with the suggestions:

```php
$suggestions = $searchResult->suggestions();
```

Each key of this collection is a suggestion name, each element is a collection of suggested terms:

```php
$titleSuggestions = $suggestions->get('title_suggest');
```

Each suggestion contains various information about the term:

```php
$firstSuggestion = $titleSuggestions->first();

// the suggestion text
$text = $firstSuggestion->getText();
// the start offset and the length in the suggest text
$offset = $firstSuggestion->getOffset();
$length = $firstSuggestion->getLength();
// an arbitrary number of options
$options = $firstSuggestion->getOptions();
// an array representation of the suggestion
$raw = $firstSuggestion->getRaw();
```

### Query Specific Methods

The query specific methods depend on the query type. It means that, for example, `must` can be used in a bool search only.

#### Bool Search

Use `boolSearch` to make a [bool request](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#query-dsl-bool-query):

```php
$searchResult = Book::boolSearch()
    ->must('match', ['title' => 'The Book'])
    ->execute()
```

Available methods:

* [filter](#filter)
* [minimumShouldMatch](#minimumshouldmatch)
* [must](#must)
* [mustNot](#mustnot)
* [onlyTrashed](#onlytrashed)
* [should](#should)
* [withTrashed](#withtrashed)

##### filter

The query defined with `filter` [must appear in the matching search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html),
but won’t contribute to the score:

```php
$searchResult = Book::boolSearch()
    ->filter('term', ['published' => '2020-06-07'])
    ->execute();
``` 

The same query with `filterRaw` method:

```php
$searchResult = Book::boolSearch()
    ->filterRaw(['term' => ['published' => '2020-06-07']])
    ->execute();
```

##### minimumShouldMatch

You can use `minimumShouldMatch` to specify [the number of `should` queries](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#bool-min-should-match)
the search results must match:

```php
$searchResult = Book::boolSearch()
    ->should('term', ['published' => '2018-04-23'])
    ->should('term', ['published' => '2020-03-07'])
    ->minimumShouldMatch(1)
    ->execute();
```

##### must

The query defined with `must` [must appear in the matching search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
and will contribute to the score:

```php
$searchResult = Book::boolSearch()
    ->must('match', ['title' => 'The Book'])
    ->execute();
```

There is also a raw version of this method:

```php
$searchResult = Book::boolSearch()
    ->mustRaw(['match' => ['title' => 'The Book']])
    ->execute();
```

##### mustNot

The query defined with `mustNot` [must not appear in the matching search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
and won’t contribute to the score:

```php
$searchResult = Book::boolSearch()
    ->mustNot('match', ['title' => 'The Book'])
    ->execute();
```

or using `mustNotRaw`:

```php
$searchResult = Book::boolSearch()
    ->mustNotRaw(['match' => ['title' => 'The Book']])
    ->execute();
```

##### onlyTrashed

Use `onlyTrashed` method to get [only soft deleted records](https://laravel.com/docs/master/scout#soft-deleting):

```php
$searchResult = Book::boolSearch()
    ->onlyTrashed()
    ->execute();
```

##### should

The query defined with `should` [should appear in the matching search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html):

```php
$searchResult = Book::boolSearch()
    ->should('match', ['title' => 'The Book'])
    ->execute();
```

You can also take advantage of `shouldRaw` method:

```php
$searchResult = Book::boolSearch()
    ->shouldRaw(['match' => ['title' => 'The Book']])
    ->execute();
```

##### withTrashed

You can use `withTrashed` to include [soft deleted records](https://laravel.com/docs/master/scout#soft-deleting)
in the search result:

```php
$searchResult = Book::boolSearch()
    ->must('match_all')
    ->withTrashed()
    ->execute()
```

#### Raw Search

Use `rawSearch` to make any custom search request:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->execute()
```

There is only one method, which is specific to raw search: `query`.

##### query

This method can be used to define raw search request query:

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->execute()
```

## Search Result

You can send a search request either by using the `execute` method:

```php
$searchResult = MyModel::boolSearch()
    ->must('match_all')
    ->execute();
```

or by using the `raw` method:

```php
$searchResult = MyModel::boolSearch()
    ->must('match_all')
    ->raw();
```

In the first case, the search results are parsed and an instance of `ElasticScoutDriverPlus\SearchResult` is returned. 
In the second case, you get the raw output.

`ElasticScoutDriverPlus\SearchResult` provides an easy access to:

* [aggregations](#aggregations)
* [documents](#documents)
* [highlights](#highlights)
* [matches](#matches)
* [models](#models)
* [suggestions](#suggestions)
* [total](#total)

#### aggregations

This method returns a collection of aggregations keyed by aggregation name:

```php
$aggregations = $searchResult->aggregations();
$maxPrice = $aggregations->get('max_price');
```

#### documents

`documents` returns a collection of matching documents:

```php
$documents = $searchResult->documents();
```

Every document has an id and an indexed content:

```php
$document = $documents->first();

$id = $document->getId();
$content = $document->getContent();
```

#### highlights

This method return a collection of highlights:

```php
$highlights = $searchResult->highlights();
```

You can use `getSnippets` to get highlighted snippets for the given field:

```php
$highlight = $highlights->first();
$snippets = $highlight->getSnippets('title');
```

#### matches

You can also retrieve a collection of matches:

```php
$matches = $searchResult->matches();
```

Each match includes the related index name, the score, the model, the document and the highlight:

```php
$firstMatch = $matches->first();

$indexName = $firstMatch->indexName();
$score = $firstMatch->score();
$model = $firstMatch->model();
$document = $firstMatch->document();
$highlight = $firstMatch->highlight();
```

#### models

Use `models` to retrieve a collection of matching models:

```php
$models = $searchResult->models();
```

**Note**, that models are lazy loaded. They are fetched from the database with a single query and only when you request them.

You can use `loadMissing` to eager load the model relations:

```php
$models->loadMissing(‘author’);
```

#### suggestions

This method returns a collection of suggestions keyed by suggestion name:

```php
$suggestions = $searchResult->suggestions();
$titleSuggestions = $suggestions->get('title_suggest');
```

Each suggestion includes a suggestion text, an offset, a length and an arbitrary number of options:

```php
$firstSuggestion = $titleSuggestions->first();

$text = $firstSuggestion->getText();
$offset = $firstSuggestion->getOffset();
$length = $firstSuggestion->getLength();
$options = $firstSuggestion->getOptions();
```

#### total

This method returns the total number of matching search results:

```php
$total = $searchResult->total();
```
