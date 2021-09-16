# Compound Queries

* [Boolean](#boolean)

## Boolean

You can use `ElasticScoutDriverPlus\Support\Query::bool()` to build a [boolean query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#query-dsl-bool-query):

```php
$query = Query::bool()->must(
    Query::match()
        ->field('title')
        ->query('The Book')
);

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [filter](#bool-filter)
* [minimumShouldMatch](#bool-minimum-should-match)
* [must](#bool-must)
* [mustNot](#bool-must-not)
* [onlyTrashed](#bool-only-trashed)
* [should](#bool-should)
* [withTrashed](#bool-with-trashed)

### <a name="bool-filter"></a> filter

The query defined with `filter` [must appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html),
but won’t contribute to the score:

```php
// you can make a query using builder
$filter = Query::term()
    ->field('published')
    ->value('2020-06-07');

// or you can define a raw query
$filter = [
    'term' => [
        'published' => '2020-06-07'
    ]
];

$query = Query::bool()->filter($filter);

$searchResult = Book::searchQuery($query)->execute();
```

The same query with `filterRaw` method:

```php
$query = Query::bool()->filterRaw([
    'term' => [
        'published' => '2020-06-07'
    ]
]);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-minimum-should-match"></a> minimumShouldMatch

You can use `minimumShouldMatch` to specify [the number of `should` queries](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#bool-min-should-match)
the documents must match:

```php
$query = Query::bool()
    ->should(Query::term()->field('published')->value('2018-04-23'))
    ->should(Query::term()->field('published')->value('2020-03-07'))
    ->minimumShouldMatch(1);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-must"></a> must

The query defined with `must` [must appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
and will contribute to the score:

```php
// you can make a query using builder
$must = Query::match()
    ->field('title')
    ->value('The Book');

// or you can define a raw query
$must = [
    'match' => [
        'title' => 'The Book'
    ]
];

$query = Query::bool()->must($must);

$searchResult = Book::searchQuery($query)->execute();
```

The same query with `mustRaw` method:

```php
$query = Query::bool()->mustRaw([
    'match' => [
        'title' => 'The Book'
    ]
]);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-must-not"></a> mustNot

The query defined with `mustNot` [must not appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
and won’t contribute to the score:

```php
// you can make a query using builder
$mustNot = Query::match()
    ->field('title')
    ->value('The Book');

// or you can define a raw query
$mustNot = [
    'match' => [
        'title' => 'The Book'
    ]
];

$query = Query::bool()->mustNot($mustNot);

$searchResult = Book::searchQuery($query)->execute();
```

The same query with `mustNotRaw` method:

```php
$query = Query::bool()->mustNotRaw([
    'match' => [
        'title' => 'The Book'
    ]
]);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-only-trashed"></a> onlyTrashed

Use `onlyTrashed` method to get [only soft deleted results](https://laravel.com/docs/master/scout#soft-deleting):

```php
$query = Query::bool()
    ->must($must)
    ->onlyTrashed();

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-should"></a> should

The query defined with `should` [should appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html):

```php
// you can make a query using builder
$should = Query::match()
    ->field('title')
    ->value('The Book');

// or you can define a raw query
$should = [
    'match' => [
        'title' => 'The Book'
    ]
];

$query = Query::bool()->should($should);

$searchResult = Book::searchQuery($query)->execute();
```

The same query with `shouldRaw` method:

```php
$query = Query::bool()->shouldRaw([
    'match' => [
        'title' => 'The Book'
    ]
]);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="bool-with-trashed"></a> withTrashed

You can use `withTrashed` to include [soft deleted results](https://laravel.com/docs/master/scout#soft-deleting)
in the search result:

```php
$query = Query::bool()
    ->must($must)
    ->withTrashed();

$searchResult = Book::searchQuery($query)->execute();
```
