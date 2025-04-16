# Available Methods

* [aggregate](#aggregate)
* [collapse](#collapse)
* [explain](#explain)
* [from](#from)
* [highlight](#highlight)
* [join](#join)
* [load](#load)
* [minScore](#minscore)
* [pointInTime](#pointintime)
* [postFilter](#postfilter)
* [preference](#preference)
* [refineModels](#refinemodels)
* [requestCache](#requestcache)
* [rescore](#rescore)
* [routing](#routing)
* [scriptFields](#scriptfields)
* [searchAfter](#searchafter)
* [searchType](#searchtype)
* [size](#size)
* [sort](#sort)
* [source](#source)
* [suggest](#suggest)
* [terminateAfter](#terminateafter)
* [trackScores](#trackscores)
* [trackTotalHits](#tracktotalhits)
* [unless](#unless)
* [when](#when)

### aggregate

This method can be used to [aggregate data](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html) 
based on a search query;

```php
$searchResult = Book::searchQuery()
    ->aggregate('max_price', [
        'max' => [
            'field' => 'price',
        ],
    ])
    ->execute();
```

Alternatively you can use the `aggregateRaw` method:

```php
$searchResult = Book::searchQuery()
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

### collapse

This method allows to [collapse](https://www.elastic.co/guide/en/elasticsearch/reference/current/collapse-search-results.html) 
search results based on field values:

```php
$searchResult = Book::searchQuery($query)
    ->collapse('author_id')
    ->sort('published', 'desc')
    ->execute();
```

There is also the `collapseRaw` method at your disposal:

```php
$searchResult = Book::searchQuery($query)
    ->collapseRaw(['field' => 'author_id'])
    ->sort('price', 'asc')
    ->execute();
```

### explain

When set to `true` every hit includes detailed information about score computation:

```php
$searchResult = Book::searchQuery($query)
    ->explain(true)
    ->execute();

$hits = $searchResult->hits();
$explanation = $hits->first()->explanation();

// every explanation includes a value, a description and details
// it is also possible to get its raw representation
$value = $explanation->value();
$description = $explanation->description();
$details = $explanation->details();
$raw = $explanation->raw();
```

### from

`from` defines [the starting document offset](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::searchQuery($query)
    ->from(5)
    ->execute();
```

### highlight

This method allows you to get [highlighted snippets](https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html#highlighting)
from one or more fields in your search results:

```php
$searchResult = Book::searchQuery($query)
    ->highlight('title')
    ->execute();
```

Use `highlightRaw` method if you need more control:

```php
$searchResult = Book::searchQuery($query)
    ->highlightRaw(['fields' => ['title' => ['number_of_fragments' => 3]]])
    ->execute();
```

Use `highlights` method to retrieve all highlights from the search result:

```php
$highlights = $searchResult->highlights();
```

You can also get a highlight for [every hit](search-results.md#hits):

```php
$hits = $searchResult->hits();
$highlight = $hits->first()->highlight();
```

The highlighted snippets can be retrieved as follows:

```php
$snippets = $highlight->snippets('title');
```

It is also possible to get a raw highlight:

```php
$raw = $highlight->raw();
```

### join

This method enables [multi indices](https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-index.html#multi-index)
search:

```php
$query = Query::bool()
    ->should(Query::match()->field('name')->query('John'))
    ->should(Query::match()->field('title')->query('The Book'))
    ->minimumShouldMatch(1);

$searchResult = Author::searchQuery($query)
    ->join(Book::class)
    ->execute();
```

In the example above, we search for an author with name `John` or a book with title `The Book` in two different indices.
Note that the result collection of models includes both types:

```php
// every model is either Author or Book
$models = $searchResult->models();
```

When searching in multiple indices, you can [boost results from a specific index](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-multiple-indices.html#index-boost)
by providing the second argument in `join` method:

```php
$searchResult = Author::searchQuery($query)
    ->join(Book::class, 2)
    ->execute();
```

### load

This method allows you to eager load model relations: 

```php
$searchResult = Book::searchQuery($query)
    ->load(['author'])
    ->execute();
```

When [searching in multiple indices](#join), you need to explicitly define the model you want the relations for:

```php
$searchResult = Book::searchQuery($query)
    ->join(Author::class)
    ->load(['author'], Book::class)
    ->load(['books'], Author::class)
    ->execute();
```

### minScore

This method allows you to [set minimum score for matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html#search-api-min-score):

```php
$searchResult = Book::searchQuery($query)
    ->minScore(0.5)
    ->execute();
```

### pointInTime

`pointInTime` allows you to set a [point in time](https://www.elastic.co/guide/en/elasticsearch/reference/current/point-in-time-api.html#point-in-time-api) 
that should be used for the search:

```php
$pit = Book::openPointInTime('1m');

$searchResult = Book::searchQuery($query)
    ->pointInTime($pit, '5m')
    ->execute();
```

**Note**, that [join](#join), [preference](#preference) and [routing](#routing) parameters are ignored 
when `pointInTime` is used.

### postFilter

`postFilter` is used to [filter search results](https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#post-filter):

```php
$postFilter = Query::term()
    ->field('published')
    ->value('2020-06-07');

$searchResult = Book::searchQuery($query)
    ->postFilter($postFilter)
    ->execute();
``` 

You can also provide a raw query in the `postFilter` method:

```php
$searchResult = Book::searchQuery($query)
    ->postFilter(['term' => ['published' => '2020-06-07']])
    ->execute();
```

### preference

`preference` defines [nodes and shards used for the search](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html#search-search-api-query-params):

```php
$searchResult = Book::searchQuery($query)
    ->preference('_local')
    ->execute();
```

### refineModels

This method allows you to set the callback where you can modify the database query.

 ```php
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

$models = Book::searchQuery($query)
    ->refineModels(function (EloquentBuilder $query) {
        $query->select(['id', 'title', 'description']);
    })
    ->execute()
    ->models();
```

When [searching in multiple indices](#join), you need to explicitly define the model for which you want to set the callback:

```php
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

$models = Book::searchQuery($query)
    ->join(Author::class)
    ->refineModels(function (EloquentBuilder $query) {
        $query->select(['id', 'title', 'description']);
    }, Book::class)
    ->refineModels(function (EloquentBuilder $query) {
        $query->select(['id', 'name', 'last_name']);
    }, Author::class)
    ->execute()
    ->models();
```

### requestCache

This method allows you to [enable or disable cache per request](https://www.elastic.co/guide/en/elasticsearch/reference/current/shard-request-cache.html#_enabling_and_disabling_caching_per_request):

```php
$searchResult = Book::searchQuery($query)
    ->requestCache(true)
    ->execute();
```

### rescore

This method allows you to [rescore](https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#rescore)
the search results. In addition, you can also use `rescoreWeights` and `rescoreWindowSize` to set `query_weight`,
`rescore_query_weight` and `window_size`:

```php
$searchResult = Book::searchQuery($query)
    ->rescore('match_phrase', [
        'message' => [
            'query' => 'the quick brown',
            'slop' => 2,
        ],
    ])
    ->rescoreWeights(0.7, 1.2)
    ->rescoreWindowSize(10)
    ->execute();
```

Alternatively you can use `rescoreRaw`:

 ```php
$searchResult = Book::searchQuery($query)
    ->rescoreRaw([
        'window_size' => 50,
        'query' => [
            'rescore_query' => [
                'match_phrase' => [
                    'message' => [
                        'query' => 'the quick brown',
                        'slop' => 2,
                    ],
                ],
            ],
            'query_weight' => 0.7,
            'rescore_query_weight' => 1.2,
        ]
    ])
    ->execute();
```

### routing

This method allows you to [search with custom routing](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-routing-field.html#_searching_with_custom_routing):

```php
$searchResult = Book::searchQuery($query)
    ->routing(['author1', 'author2'])
    ->execute();
```

### scriptFields

This method allows you to [retrieve a script evaluation](https://www.elastic.co/docs/reference/elasticsearch/rest-apis/retrieve-selected-fields#script-fields):

```php
$searchResult = Book::searchQuery($query)
    ->scriptFields('final_price', [
        'lang' => 'painless',
        'source' => "doc['price'].value * params.factor",
        'params' => [
            'factor' => 2,
        ],
    ])
    ->execute();
```

You can also make use of the `scriptFieldsRaw` method:

```php
$searchResult = Book::searchQuery($query)
    ->scriptFieldsRaw([
        'final_price' => [
            'script' => [
                'lang' => 'painless',
                'source' => "doc['price'].value * params.factor",
                'params' => [
                    'factor' => 2,
                ],
            ],
        ],
    ])
    ->execute();
```

You can access evaluated values as follows:

```php
$finalPrice = $searchResult->hits()->first()->raw()['fields']['final_price'];
```

### searchAfter

You can use `searchAfter` [to retrieve the next page of hits using a set of sort values from the previous page](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html#search-after):

```php
$firstPage = Book::searchQuery($query)
    ->pointInTime($pit)
    ->execute();

$searchAfter = $firstPage->hits()->last()->sort();

$secondPage = Book::searchQuery($query)
    ->pointInTime($pit)
    ->searchAfter($searchAfter)
    ->execute();
```

### searchType

`searchType` defines [how distributed term frequencies are calculated for relevance scoring](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html#search-search-api-query-params):

```php
$searchResult = Book::searchQuery($query)
    ->searchType('query_then_fetch')
    ->execute();
```

### size

`size` method [limits the number of hits to return](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::searchQuery($query)
    ->size(2)
    ->execute();
```

### sort

This method [sorts](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html) the search results:

```php
$searchResult = Book::searchQuery($query)
    ->sort('price', 'asc')
    ->execute();
```

In case you need more advanced sorting algorithm use `sortRaw`:

```php
$searchResult = Book::searchQuery($query)
    ->sortRaw([['price' => 'asc'], ['published' => 'asc']])
    ->execute();
```

### source

This method allows you to [select what document fields of the source are returned](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-fields.html#source-filtering):

```php
$searchResult = Book::searchQuery($query)
    ->source(['title', 'description'])
    ->execute();
```

`sourceRaw` allows you to use a single wildcard pattern, an array of fields or a boolean value in case you want to
exclude document source from the result:

```php
$searchResult = Book::searchQuery($query)
    ->sourceRaw(false)
    ->execute();
```

### suggest

This method can be used to [get similar looking terms](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#search-suggesters)
based on the provided text:

```php
$searchResult = Book::searchQuery(Query::matchNone())
    ->suggest('title_suggest', ['text' => 'book', 'term' => ['field' => 'title']])
    ->execute();
```

The same query with `suggestRaw` method:

```php
$searchResult = Book::searchQuery(Query::matchNone())
    ->suggestRaw(['title_suggest' => ['text' => 'book', 'term' => ['field' => 'title']]])
    ->execute();
```

You can use the `suggestions` method to retrieve suggestions from the search result:

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
$text = $firstSuggestion->text();
// the start offset and the length of the suggested text
$offset = $firstSuggestion->offset();
$length = $firstSuggestion->length();
// an arbitrary number of options
$options = $firstSuggestion->options();
// related models (only some suggesters support this feature)
$models = $firstSuggestion->models();
// an array representation of the suggestion
$raw = $firstSuggestion->raw();
```

### terminateAfter

This method allows you to set the maximum number of documents to collect for each shard:

```php
$searchResult = Book::searchQuery($query)
    ->terminateAfter(10)
    ->execute();
```

### trackScores

This method forces [scores to be computed and tracked](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html#_track_scores):

```php
$searchResult = Book::searchQuery($query)
    ->trackScores(true)
    ->execute();
```

### trackTotalHits

This method allows you to [control how the total number of hits should be tracked](https://www.elastic.co/guide/en/elasticsearch//reference/current/search-your-data.html#track-total-hits):

```php
$searchResult = Book::searchQuery($query)
    ->trackTotalHits(true)
    ->execute();
```

### unless

This method will execute the given callback unless the first argument given to the method evaluates to `true`:

```php
$searchResult = Book::searchQuery($query)
    ->unless($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    })
    ->execute();
```

You may also pass another closure as a third argument to the `unless` method. This closure will be only executed
if the first argument evaluates to `true`:

```php
$searchResult = Book::searchQuery($query)
    ->unless($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    }, function ($builder) {
         return $builder->sort('price', 'asc');
     })
    ->execute();
```

### when

This method will execute the given callback when the first argument given to the method evaluates to `true`:

```php
$searchResult = Book::searchQuery($query)
    ->when($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    })
    ->execute();
```

You may also pass another closure as a third argument to the `when` method. This closure will be only executed
if the first argument evaluates to `false`:

```php
$searchResult = Book::searchQuery($query)
    ->when($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    }, function ($builder) {
         return $builder->sort('price', 'asc');
     })
    ->execute();
```
