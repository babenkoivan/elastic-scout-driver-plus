# Multi Search

Independent searches can be sent to Elasticsearch in a single request using
[the multi search API](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-multi-search.html).
This saves a round trip per search, which is noticeable when several models are
searched at once, for example in a global search-as-you-type feature.

Add every query to `MultiSearchBuilder` and execute them together:

```php
use Elastic\ScoutDriverPlus\Builders\MultiSearchBuilder;

$searchResults = (new MultiSearchBuilder())
    ->add(Book::searchQuery($bookQuery)->size(6)->load(['author']))
    ->add(Author::searchQuery($authorQuery)->size(2))
    ->execute();
```

`execute` returns a collection of `Elastic\ScoutDriverPlus\Decorators\SearchResult`
instances in the order the queries were added, each of them supporting
[everything a single search result does](search-results.md):

```php
[$bookResults, $authorResults] = $searchResults->all();

$books = $bookResults->models();
$total = $authorResults->total();
```

You can also label the queries and access the results by name, which is more
robust when the list of queries is built conditionally:

```php
$searchResults = (new MultiSearchBuilder())
    ->add(Book::searchQuery($bookQuery), 'books')
    ->when($user->can('viewAuthors'), fn ($builder) => $builder->add(Author::searchQuery($authorQuery), 'authors'))
    ->execute();

$books = $searchResults->get('books')->models();
```

Raw responses are available as well:

```php
$raw = (new MultiSearchBuilder())
    ->add(Book::searchQuery($bookQuery))
    ->add(Author::searchQuery($authorQuery))
    ->raw();
```

If no query was added, `execute` and `raw` return an empty collection without
sending any request.

Elasticsearch answers a multi search with a per-query `error` object rather than
an HTTP error when one of the queries fails. In that case
`Elastic\Adapter\Exceptions\SearchOperationException` is thrown, and its
`rawResult` method gives access to the full response, including the results of
the queries that succeeded:

```php
use Elastic\Adapter\Exceptions\SearchOperationException;

try {
    $searchResults = (new MultiSearchBuilder())
        ->add(Book::searchQuery($bookQuery))
        ->add(Author::searchQuery($authorQuery))
        ->execute();
} catch (SearchOperationException $exception) {
    $responses = $exception->rawResult()['responses'];
}
```

Note that a multi search is a single request, hence all the queries must use the
same connection. Models with a different `searchableConnection` cannot be mixed
in one `MultiSearchBuilder`.

Note also that only the Elasticsearch round trips are batched. Models are still
retrieved from the database with one query per index per result, as the `load`
and `refineModels` settings differ from query to query.
