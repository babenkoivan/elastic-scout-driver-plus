# Generic Methods

* [aggregate](#aggregate)
* [boostIndex](#boostindex)
* [collapse](#collapse)
* [from](#from)
* [highlight](#highlight)
* [join](#join)
* [load](#load)
* [postFilter](#postfilter)
* [size](#size)
* [sort](#sort)
* [rescore](#rescore)
* [source](#source)
* [suggest](#suggest)
* [trackScores](#trackscores)
* [trackTotalHits](#tracktotalhits)
* [when](#when)

### aggregate

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

### boostIndex

When searching in multiple indices, you can use this method to [boost results from a specific index](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-multiple-indices.html#index-boost):

```php
$searchResult = Author::boolSearch()
    ->join(Book::class)
    ->boostIndex(Book::class, 2)
    ->must('match_all')
    ->execute();
```

### collapse

This method allows to [collapse](https://www.elastic.co/guide/en/elasticsearch/reference/current/collapse-search-results.html) 
search results based on field values:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->collapse('author_id')
    ->sort('published', 'desc')
    ->execute();
```

There is also `collapseRaw` method at your disposal:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->collapseRaw(['field' => 'author_id'])
    ->sort('price', 'asc')
    ->execute();
```

### from

`from` method defines [the starting document offset](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->from(5)
    ->execute();
```

### highlight

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

Use `highlights` method to retrieve all highlights from the search result:

```php
$highlights = $searchResult->highlights();
```

You can also get a highlight for [every respective match](search-results.md#matches):

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

### join

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

### load

This method allows you to eager load model relations: 

```php
$searchResult = Book::rawSearch()
    ->query(['match' => ['title' => 'The Book']])
    ->load(['author'])
    ->execute();
```

When [searching in multiple indices](#join), you need to explicitly define the model you want the relations for:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new stdClass()])
    ->join(Author::class)
    ->load(['author'], Book::class)
    ->load(['books'], Author::class)
    ->execute();
```

### postFilter

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

### size

`size` method [limits the number of hits to return](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->size(2)
    ->execute();
```

### sort

This method [sorts](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html) the search results:

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
    ->sortRaw([['price' => 'asc'], ['published' => 'asc']])
    ->execute();
```

### rescore

This method allows you to [rescore](https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#rescore) the search results:

You can also use `rescoreWeights` and `rescoreWindowSize` to set the query_weight, rescore_query_weight and window_size.

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->rescore($type = 'match_phrase', [
        'message' => [
            'query' => 'the quick brown',
            'slop' => 2,
        ],
    ])
    ->rescoreWeights($queryWeight = 0.7, $rescoreQueryWeight = 1.2)
    ->rescoreWindowSize($windowSize = 10)
    ->execute();
```

Use `rescoreRaw` method if you need more control:

 ```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
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

### source

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

### suggest

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
    ->suggestRaw(['title_suggest' => ['text' => 'book', 'term' => ['field' => 'title']]])
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

### trackScores

This method forces [scores to be computed and tracked](https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html#_track_scores):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->trackScores(true)
    ->execute();
```

### trackTotalHits

This method allows you to [control how the total number of hits should be tracked](https://www.elastic.co/guide/en/elasticsearch//reference/current/search-your-data.html#track-total-hits):

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->trackTotalHits(true)
    ->execute();
```

### when

This method can be used to apply certain clauses based on another condition:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->when($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    })
    ->execute();
```

You may also pass another closure as a third argument to the `when` method. This closure will only execute
if the first argument evaluates as `false`:

```php
$searchResult = Book::rawSearch()
    ->query(['match_all' => new \stdClass()])
    ->when($orderBy, function ($builder, $orderBy) {
        return $builder->sort($orderBy, 'asc');
    }, function ($builder) {
         return $builder->sort('price', 'asc');
     })
    ->execute();
```
