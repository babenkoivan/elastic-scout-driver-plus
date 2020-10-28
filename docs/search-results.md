# Search Results

Whenever a search request is ready to be executed, you have several options:

1. You can get raw results:

 ```php
$rawSearchResults = MyModel::boolSearch()
    ->must('match_all')
    ->raw();
 ```

2. You can paginate your search results:

```php
$paginator = MyModel::boolSearch()
    ->must('match_all')
    ->paginate(10);
```

Unlike the [standard Scout pagination](https://laravel.com/docs/master/scout#pagination), Elastic Scout Driver Plus 
paginates [matches](#matches) and not the models. 

Also **note**, that [from](generic-methods.md#from) and [size](generic-methods.md#size) are ignored when paginating the search results.

3. You can transform raw results into `ElasticScoutDriverPlus\SearchResult` instance:

```php
$searchResult = MyModel::boolSearch()
    ->must('match_all')
    ->execute();
```

`ElasticScoutDriverPlus\SearchResult` provides an easy access to:

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
