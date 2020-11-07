# Joining Queries

* [Nested](#nested)

## Nested

Use `nestedSearch` to [search in nested fields](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html#query-dsl-nested-query):

```php
$searchResult = Book::nestedSearch()
    ->path('author')
    ->query(['match' => ['author.name' => 'Steven']])
    ->execute();
```

Available methods:
* [ignoreUnmapped](#nested-ignore-unmapped)
* [path](#nested-path)
* [query](#nested-query)
* [scoreMode](#nested-score-mode)

### <a name="nested-ignore-unmapped"></a> ignoreUnmapped

You can use `ignoreUnmapped` to query multiple indices that may not contain the field `path`: 

```php
$searchResult = Book::nestedSearch()
    ->path('author')
    ->query(['match' => ['author.name' => 'Steven']])
    ->ignoreUnmapped(true)
    ->execute();
```
 
### <a name="nested-path"></a> path

Use `path` to set a path to the nested field you wish to search in:

```php
$searchResult = Book::nestedSearch()
    ->path('author')
    ->query(['match' => ['author.name' => 'Steven']])
    ->execute();
``` 

### <a name="nested-query"></a> query

`query` defines a raw query you wish to run on the nested field:

```php
$searchResult = Book::nestedSearch()
    ->path('author')
    ->query(['match' => ['author.name' => 'Steven']])
    ->execute();
``` 

### <a name="nested-score-mode"></a> scoreMode

`scoreMode` is used to set a scoring mode:

```php
$searchResult = Book::nestedSearch()
    ->path('author')
    ->query(['match' => ['author.name' => 'Steven']])
    ->scoreMode('avg')
    ->execute();
```
