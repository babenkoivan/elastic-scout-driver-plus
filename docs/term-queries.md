# Term Queries

* [Exists](#exists)
* [Fuzzy](#fuzzy)
* [Ids](#ids)
* [Prefix](#prefix)
* [Range](#range)
* [Regexp](#regexp)
* [Term](#term)
* [Terms](#terms)
* [Wildcard](#wildcard)

## Exists

You can use `ElasticScoutDriverPlus\Support\Query::exists()` to build a query that matches documents, which
[contain an indexed value for a `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html#query-dsl-exists-query):

```php
$query = Query::exists()->field('description');

$searchResult = Book::searchQuery($query)->execute();
```

## Fuzzy

You can use `ElasticScoutDriverPlus\Support\Query::fuzzy()` to build a query that matches documents, which
[contain terms similar to the search term](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#query-dsl-fuzzy-query):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko');

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [field](#fuzzy-field)
* [fuzziness](#fuzzy-fuzziness)
* [maxExpansions](#fuzzy-max-expansions)
* [prefixLength](#fuzzy-prefix-length)
* [rewrite](#fuzzy-rewrite)
* [transpositions](#fuzzy-transpositions)
* [value](#fuzzy-value)

### <a name="fuzzy-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-top-level-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-fuzziness"></a> fuzziness

`fuzziness` controls [maximum edit distance allowed for matching](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko')
    ->fuzziness('AUTO');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-max-expansions"></a> maxExpansions

You can use `maxExpansions` to specify [maximum number of terms to which the query will expand](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko')
    ->maxExpansions(50);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-prefix-length"></a> prefixLength

`prefixLength` is used to determine [the number of beginning characters left unchanged when creating expansions](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko')
    ->prefixLength(0);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko')
    ->rewrite('constant_score');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-transpositions"></a> transpositions

`transpositions` allows to [include transpositions of two adjacent characters](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko')
    ->transpositions(true);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="fuzzy-value"></a> value

With `value` you can define a [term you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$query = Query::fuzzy()
    ->field('title')
    ->value('boko');

$searchResult = Book::searchQuery($query)->execute();
```

## Ids

You can use `ElasticScoutDriverPlus\Support\Query::ids()` to build a query that matches documents
[based on their IDs](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html#query-dsl-ids-query):

```php
$query = Query::ids()->values(['1', '2', '3']);

$searchResult = Book::searchQuery($query)->execute();
```

## Prefix

You can use `ElasticScoutDriverPlus\Support\Query::prefix()` to build a query that matches documents, which
[contain a specific prefix in a provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#query-dsl-prefix-query):

```php
$query = Query::prefix()
    ->field('title')
    ->value('boo');

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [field](#prefix-field)
* [rewrite](#prefix-rewrite)
* [value](#prefix-value)

### <a name="prefix-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-top-level-params):

```php
$query = Query::prefix()
    ->field('title')
    ->value('boo');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="prefix-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-field-params):

```php
$query = Query::prefix()
    ->field('title')
    ->value('boo')
    ->rewrite('constant_score');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="prefix-value"></a> value

With `value` you can define [beginning characters of terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-field-params):

```php
$query = Query::prefix()
    ->field('title')
    ->value('boo');

$searchResult = Book::searchQuery($query)->execute();
```

## Range

You can use `ElasticScoutDriverPlus\Support\Query::range()` to build a query that matches documents, which
[contain terms within a provided range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#query-dsl-range-query):

```php
$query = Query::range()
    ->field('price')
    ->gt(100);

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [boost](#range-boost)
* [field](#range-field)
* [format](#range-format)
* [gt](#range-gt)
* [gte](#range-gte)
* [lt](#range-lt)
* [lte](#range-lte)
* [relation](#range-relation)
* [timeZone](#range-time-zone)

### <a name="range-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('price')
    ->gt(100)
    ->boost(2);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-top-level-params):

```php
$query = Query::range()
    ->field('price')
    ->gt(100);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-format"></a> format

`format` is used to [convert date values in the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('updated_at')
    ->gt('2020-10-18')
    ->format('yyyy-MM-dd');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-gt"></a> gt

`gt` defines a [greater than range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('price')
    ->gt(100);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-gte"></a> gte

`gte` defines a [greater than or equal to range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('price')
    ->gte(100);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-lt"></a> lt

`lt` defines a [less than range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('price')
    ->lt(100);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-lte"></a> lte

`lte` defines a [less than or equal to range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('price')
    ->lte(100);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-relation"></a> relation

You can use `relation` to specify how the range query [matches values for range fields](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params): 

```php
$query = Query::range()
    ->field('price')
    ->gt(50)
    ->lt(100)
    ->relation('INTERSECTS');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="range-time-zone"></a> timeZone

`timeZone` is used to [convert date values in the query to UTC](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$query = Query::range()
    ->field('updated_at')
    ->gt('2020-10-18')
    ->timeZone('+01:00');

$searchResult = Book::searchQuery($query)->execute();
```

## Regexp

You can use `ElasticScoutDriverPlus\Support\Query::regexp()` to build a query that matches documents, which
[contain terms corresponding to regular expression](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#query-dsl-regexp-query):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k');

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [field](#regexp-field)
* [flags](#regexp-flags)
* [maxDeterminizedStates](#regexp-max-determinized-states)
* [rewrite](#regexp-rewrite)
* [value](#regexp-value)

### <a name="regexp-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-top-level-params):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="regexp-flags"></a> flags

Use `flags` to [enable optional operators for the regular expression](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k')
    ->flags('ALL');

$searchResult = Book::searchQuery($query)->execute();
``` 

### <a name="regexp-max-determinized-states"></a> maxDeterminizedStates

`maxDeterminizedStates` defines the [maximum number of automation states required for the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k')
    ->maxDeterminizedStates(10000);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="regexp-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k')
    ->rewrite('constant_score');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="regexp-value"></a> value

With `value` you can define a [regular expression for terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$query = Query::regexp()
    ->field('title')
    ->value('b.*k');

$searchResult = Book::searchQuery($query)->execute();
```

## Term

You can use `ElasticScoutDriverPlus\Support\Query::term()` to build a query that matches documents, which
[contain an exact term in a provided field](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#query-dsl-term-query):

```php
$query = Query::term()
    ->field('price')
    ->value(300);

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [boost](#term-boost)
* [field](#term-field)
* [value](#term-value)

### <a name="term-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-field-params):

```php
$query = Query::term()
    ->field('price')
    ->value(300)
    ->boost(2);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="term-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-top-level-params):

```php
$query = Query::term()
    ->field('price')
    ->value(300);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="term-value"></a> value

With `value` you can define a [term you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-field-params):

```php
$query = Query::term()
    ->field('price')
    ->value(300);

$searchResult = Book::searchQuery($query)->execute();
```

## Terms

You can use `ElasticScoutDriverPlus\Support\Query::terms()` to build a query that matches documents, which
[contain one or more exact terms in a provided field](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#query-dsl-terms-query):

```php
$query = Query::terms()
    ->field('tags')
    ->values(['available', 'new']);

$searchResult = Book::searchQuery($query)->execute();
```

Available methods: 

* [boost](#terms-boost)
* [field](#terms-field)
* [values](#terms-values)

### <a name="terms-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#terms-top-level-params):

```php
$query = Query::terms()
    ->field('tags')
    ->values(['available', 'new'])
    ->boost(2);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="terms-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#terms-top-level-params):

```php
$query = Query::terms()
    ->field('tags')
    ->values(['available', 'new']);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="terms-values"></a> values

With `value` you can define [terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#terms-top-level-params):

```php
$query = Query::terms()
    ->field('tags')
    ->values(['available', 'new']);

$searchResult = Book::searchQuery($query)->execute();
```

## Wildcard

You can use `ElasticScoutDriverPlus\Support\Query::wildcard()` to build a query that matches documents, which
[contain terms corresponding to wildcard pattern](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#query-dsl-wildcard-query):

```php
$query = ElasticScoutDriverPlus\Support\Query::wildcard()
    ->field('title')
    ->value('bo*k');

$searchResult = Book::searchQuery($query)->execute();
```

Available methods:

* [boost](#wildcard-boost)
* [field](#wildcard-field)
* [rewrite](#wildcard-rewrite)
* [value](#wildcard-value)

### <a name="wildcard-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$query = ElasticScoutDriverPlus\Support\Query::wildcard()
    ->field('title')
    ->value('bo*k')
    ->boost(2);

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="wildcard-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-top-level-params):

```php
$query = ElasticScoutDriverPlus\Support\Query::wildcard()
    ->field('title')
    ->value('bo*k');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="wildcard-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$query = ElasticScoutDriverPlus\Support\Query::wildcard()
    ->field('title')
    ->value('bo*k')
    ->rewrite('constant_score');

$searchResult = Book::searchQuery($query)->execute();
```

### <a name="wildcard-value"></a> value

With `value` you can define a [wildcard pattern for terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$query = ElasticScoutDriverPlus\Support\Query::wildcard()
    ->field('title')
    ->value('bo*k');

$searchResult = Book::searchQuery($query)->execute();
```
