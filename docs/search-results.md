# Search Results

Whenever your search request is ready to be executed, you have several options:

## Raw

You can get a raw response from Elasticsearch:

 ```php
$rawSearchResults = MyModel::boolSearch()
    ->must('match_all')
    ->raw();
 ```

## SearchResult

You can execute the request and get `ElasticScoutDriverPlus\SearchResult` instance in return:

```php
$searchResult = MyModel::boolSearch()
    ->must('match_all')
    ->execute();
```

`SearchResult` provides easy access to:

* [aggregations](#aggregations)
* [documents](#documents)
* [highlights](#highlights)
* [matches](#matches)
* [models](#models)
* [suggestions](#suggestions)
* [total](#total)

### aggregations

This method returns a collection of aggregations keyed by aggregation name:

```php
$aggregations = $searchResult->aggregations();
$maxPrice = $aggregations->get('max_price');
```

### documents

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

### highlights

This method returns a collection of highlights:

```php
$highlights = $searchResult->highlights();
```

You can use `getSnippets` to get highlighted snippets for the given field:

```php
$highlight = $highlights->first();
$snippets = $highlight->getSnippets('title');
```

### matches

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

Furthermore, you can get a raw representation of the respective hit:

```php
$raw = $firstMatch->raw();
```

### models

Use `models` to retrieve a collection of matching models:

```php
$models = $searchResult->models();
```

**Note**, that models are lazy loaded. They are fetched from the database with a single query and only when you request them.

### suggestions

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

### total

This method returns the total number of matching documents:

```php
$total = $searchResult->total();
```

## Pagination

Finally, you can paginate the search results:

```php
$paginator = MyModel::boolSearch()
    ->must('match_all')
    ->paginate(10);
```

The paginator provides the same interface as `SearchResult`, which means that you can access models, highlights, etc.:

```php
$models = $paginator->models();
```

Unlike the [standard Scout paginator](https://laravel.com/docs/master/scout#pagination), Elastic Scout Driver Plus
paginates [matches](#matches) and not the models:

```php
foreach ($paginator as $match) {
    $model = $match->model();
}
```

**Note** that [from](generic-methods.md#from) and [size](generic-methods.md#size) are ignored when paginating the search results.
