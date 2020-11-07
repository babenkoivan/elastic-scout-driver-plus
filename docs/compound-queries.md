# Compound Queries

* [Boolean](#boolean)

## Boolean

Use `boolSearch` to make a [boolean query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#query-dsl-bool-query):

```php
$searchResult = Book::boolSearch()
    ->must('match', ['title' => 'The Book'])
    ->execute();
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

### <a name="bool-minimum-should-match"></a> minimumShouldMatch

You can use `minimumShouldMatch` to specify [the number of `should` queries](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html#bool-min-should-match)
the documents must match:

```php
$searchResult = Book::boolSearch()
    ->should('term', ['published' => '2018-04-23'])
    ->should('term', ['published' => '2020-03-07'])
    ->minimumShouldMatch(1)
    ->execute();
```

### <a name="bool-must"></a> must

The query defined with `must` [must appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
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

### <a name="bool-must-not"></a> mustNot

The query defined with `mustNot` [must not appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html)
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

### <a name="bool-only-trashed"></a> onlyTrashed

Use `onlyTrashed` method to get [only soft deleted results](https://laravel.com/docs/master/scout#soft-deleting):

```php
$searchResult = Book::boolSearch()
    ->onlyTrashed()
    ->execute();
```

### <a name="bool-should"></a> should

The query defined with `should` [should appear in the matching documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html):

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

### <a name="bool-with-trashed"></a> withTrashed

You can use `withTrashed` to include [soft deleted results](https://laravel.com/docs/master/scout#soft-deleting)
in the search result:

```php
$searchResult = Book::boolSearch()
    ->must('match_all')
    ->withTrashed()
    ->execute();
```
