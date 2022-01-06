# Joining Queries

* [Nested](#nested)

## Nested

You can use `ElasticScoutDriverPlus\Support\Query::nested()` to build a [nested query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html#query-dsl-nested-query):

```php
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'));

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [ignoreUnmapped](#nested-ignore-unmapped)
* [innerHits](#nested-inner-hits)
* [path](#nested-path)
* [query](#nested-query)
* [scoreMode](#nested-score-mode)

### <a name="nested-ignore-unmapped"></a> ignoreUnmapped

You can use `ignoreUnmapped` to query multiple indices that may not contain the field `path`:

```php
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'))
    ->ignoreUnmapped(true);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="nested-inner-hits"></a> innerHits

`innerHits` support [the following options](https://www.elastic.co/guide/en/elasticsearch/reference/current/inner-hits.html#inner-hits-options): from, size, sort, name and some per document features:

```php
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'))
    ->innerHits(['name' => 'authors']);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="nested-path"></a> path

Use `path` to set a path to the nested field you wish to search in:

```php
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'));

$searchResult = Book::searchQuery($query)->execute();
``` 

### <a name="nested-query"></a> query

`query` defines a query you wish to run on the nested field:

```php

// you can make a query using builder
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'));

// or you can define a raw query
$query = [
    'nested' => [
        'path' => 'author',
        'query' => [
            'match' => [
               'author.name' => 'Steven'
            ]
        ]
    ]
];

$searchResult = Book::searchQuery($query)->execute();
``` 

### <a name="nested-score-mode"></a> scoreMode

`scoreMode` is used to set a scoring mode:

```php
$query = Query::nested()
    ->path('author')
    ->query(Query::match()->field('author.name')->field('Steven'))
    ->scoreMode('avg');

$searchResult = Book::searchQuery($query)->execute();
```
