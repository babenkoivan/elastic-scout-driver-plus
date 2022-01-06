# Search Results

When your search query is ready to be executed, you have several options:

## Raw

You can get a raw response from Elasticsearch:

 ```php
$raw = Book::searchQuery($query)->raw();
 ```

## SearchResult

You can execute the query and get `ElasticScoutDriverPlus\Decorators\SearchResult` instance in return:

```php
$searchResult = Book::searchQuery($query)->execute();
```

`SearchResult` provides easy access to:

* [aggregations](#aggregations)
* [documents](#documents)
* [highlights](#highlights)
* [hits](#hits)
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

Every document has an id and content:

```php
$document = $documents->first();

$id = $document->id();
$content = $document->content();
```

### highlights

This method returns a collection of highlights:

```php
$highlights = $searchResult->highlights();
```

You can use `snippets` to get highlighted snippets for the given field:

```php
$highlight = $highlights->first();
$snippets = $highlight->snippets('title');
```

### hits

You can retrieve a collection of hits:

```php
$hits = $searchResult->hits();
```

Each hit provides access to the related index name, the score, the model, the document, the highlight and the inner hits:

```php
$hit = $hits->first();

$indexName = $hit->indexName();
$score = $hit->score();
$model = $hit->model();
$document = $hit->document();
$highlight = $hit->highlight();
$innerHits = $hit->innerHits();
```

Furthermore, you can get a raw representation of the respective hit:

```php
$raw = $hit->raw();
```

### models

You can use `models` to retrieve a collection of matching models:

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

$text = $firstSuggestion->text();
$offset = $firstSuggestion->offset();
$length = $firstSuggestion->length();
$options = $firstSuggestion->options();
```

### total

This method returns the total number of matching documents:

```php
$total = $searchResult->total();
```

## Pagination

Finally, you can paginate search results:

```php
$paginator = Book::searchQuery($query)->paginate(10);
```

The paginator provides the same interface as `SearchResult`, which means that you can access models, highlights, etc.:

```php
$models = $paginator->models();
```

However, Elastic Scout Driver Plus by default paginates [hits](#hits) and not models, this behaviour can be changed:

```php
// paginate hits
$paginator = Book::searchQuery($query)
    ->paginate(10);

foreach ($paginator as $hit) {
    $model = $hit->model();
}

// paginate models
$paginator = Book::searchQuery($query)
    ->paginate(10)
    ->onlyModels();

foreach ($paginator as $model) {
    $id = $model->id;
}

// paginated documents
$paginator = Book::searchQuery($query)
    ->paginate(10)
    ->onlyDocuments();

foreach ($paginator as $document) {
    $id = $document->id();
}
```

**Note** that [from](available-methods.md#from) and [size](available-methods.md#size) are ignored when paginating search results.
