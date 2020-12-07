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

`existsSearch` returns documents that [contain an indexed value for a `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html#query-dsl-exists-query):

```php
$searchResult = Book::existsSearch()
    ->field('description')
    ->execute();
```

`ExistsQueryBuilder` doesn't provide any additional methods.

## Fuzzy

`fuzzySearch` returns documents that [contain terms similar to the search term](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#query-dsl-fuzzy-query):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->execute();
```

Available methods provided by `FuzzyQueryBuilder`:

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
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->execute();
```

### <a name="fuzzy-fuzziness"></a> fuzziness

`fuzziness` controls [maximum edit distance allowed for matching](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->fuzziness('AUTO')
    ->execute();
```

### <a name="fuzzy-max-expansions"></a> maxExpansions

You can use `maxExpansions` to specify [maximum number of terms to which the query will expand](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->maxExpansions(50)
    ->execute();
```

### <a name="fuzzy-prefix-length"></a> prefixLength

`prefixLength` is used to determine [the number of beginning characters left unchanged when creating expansions](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->prefixLength(0)
    ->execute();
```

### <a name="fuzzy-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->rewrite('constant_score')
    ->execute();
```

### <a name="fuzzy-transpositions"></a> transpositions

`transpositions` allows to [include transpositions of two adjacent characters](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->transpositions(true)
    ->execute();
```

### <a name="fuzzy-value"></a> value

With `value` you can define a [term you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html#fuzzy-query-field-params):

```php
$searchResult = Book::fuzzySearch()
    ->field('title')
    ->value('boko')
    ->execute();
```

## Ids

`idsSearch` returns documents [based on their IDs](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html#query-dsl-ids-query):

```php
$searchResult = Book::idsSearch()
    ->values(['1', '2', '3'])
    ->execute();
```

## Prefix

`prefixSearch` returns documents that [contain a specific prefix in a provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#query-dsl-prefix-query):

```php
$searchResult = Book::prefixSearch()
    ->field('title')
    ->value('boo')
    ->execute();
```

Available methods provided by `PrefixQueryBuilder`:

* [field](#prefix-field)
* [rewrite](#prefix-rewrite)
* [value](#prefix-value)

### <a name="prefix-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-top-level-params):

```php
$searchResult = Book::prefixSearch()
    ->field('title')
    ->value('boo')
    ->execute();
```

### <a name="prefix-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-field-params):

```php
$searchResult = Book::prefixSearch()
    ->field('title')
    ->value('boo')
    ->rewrite('constant_score')
    ->execute();
```

### <a name="prefix-value"></a> value

With `value` you can define [beginning characters of terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html#prefix-query-field-params):

```php
$searchResult = Book::prefixSearch()
    ->field('title')
    ->value('boo')
    ->execute();
```

## Range

`rangeSearch` returns documents that [contain terms within a provided range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#query-dsl-range-query):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gt(100)
    ->execute();
```

Available methods provided by `RangeQueryBuilder`:

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
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gt(100)
    ->boost(2)
    ->execute();
```

### <a name="range-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-top-level-params):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gt(100)
    ->execute();
```

### <a name="range-format"></a> format

`format` is used to [convert date values in the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('updated_at')
    ->gt('2020-10-18')
    ->format('yyyy-MM-dd')
    ->execute();
```

### <a name="range-gt"></a> gt

`gt` defines a [greater than range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gt(100)
    ->execute();
```

### <a name="range-gte"></a> gte

`gte` defines a [greater than or equal to range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gte(100)
    ->execute();
```

### <a name="range-lt"></a> lt

`lt` defines a [less than range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->lt(100)
    ->execute();
```

### <a name="range-lte"></a> lte

`lte` defines a [less than or equal to range](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->lte(100)
    ->execute();
```

### <a name="range-relation"></a> relation

You can use `relation` to specify how the range query [matches values for range fields](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params): 

```php
$searchResult = Book::rangeSearch()
    ->field('price')
    ->gt(50)
    ->lt(100)
    ->relation('INTERSECTS')
    ->execute();
```

### <a name="range-time-zone"></a> timeZone

`timeZone` is used to [convert date values in the query to UTC](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-field-params):

```php
$searchResult = Book::rangeSearch()
    ->field('updated_at')
    ->gt('2020-10-18')
    ->timeZone('+01:00')
    ->execute();
```

## Regexp

`regexpSearch` returns documents that [contain terms matching a regular expression](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#query-dsl-regexp-query):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->execute();
```

Available methods provided by `RegexpQueryBuilder`:

* [field](#regexp-field)
* [flags](#regexp-flags)
* [maxDeterminizedStates](#regexp-max-determinized-states)
* [rewrite](#regexp-rewrite)
* [value](#regexp-value)

### <a name="regexp-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-top-level-params):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->execute();
```

### <a name="regexp-flags"></a> flags

Use `flags` to [enable optional operators for the regular expression](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->flags('ALL')
    ->execute();
``` 

### <a name="regexp-max-determinized-states"></a> maxDeterminizedStates

`maxDeterminizedStates` defines the [maximum number of automation states required for the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->maxDeterminizedStates(10000)
    ->execute();
```

### <a name="regexp-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->rewrite('constant_score')
    ->execute();
```

### <a name="regexp-value"></a> value

With `value` you can define a [regular expression for terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-query-field-params):

```php
$searchResult = Book::regexpSearch()
    ->field('title')
    ->value('b.*k')
    ->execute();
```

## Term

`termSearch` returns documents that [contain an exact term in a provided field](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#query-dsl-term-query):

```php
$searchResult = Book::termSearch()
    ->field('price')
    ->value('300')
    ->execute();
```

Available methods provided by `TermQueryBuilder`:

* [boost](#term-boost)
* [field](#term-field)
* [value](#term-value)

### <a name="term-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-field-params):

```php
$searchResult = Book::termSearch()
    ->field('price')
    ->value('300')
    ->boost(2)
    ->execute();
```

### <a name="term-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-top-level-params):

```php
$searchResult = Book::termSearch()
    ->field('price')
    ->value('300')
    ->execute();
```

### <a name="term-value"></a> value

With `value` you can define a [term you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html#term-field-params):

```php
$searchResult = Book::termSearch()
    ->field('price')
    ->value('300')
    ->execute();
```

## Terms

`termsSearch` returns documents that [contain one or more exact terms in a provided field](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#query-dsl-terms-query):

```php
$searchResult = Book::termsSearch()
    ->terms('tags', ['available', 'new'])
    ->execute();
```

Available methods provided by `TermsQueryBuilder`: 

* [boost](#terms-boost)
* [terms](#terms-terms)

### <a name="terms-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#terms-top-level-params):

```php
$searchResult = Book::termsSearch()
    ->terms('tags', ['available', 'new'])
    ->boost(2)
    ->execute();
```

### <a name="terms-terms"></a> terms

Use `terms` to define array of terms you [wish to find in the provided field](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html#terms-top-level-params):

```php
$searchResult = Book::termsSearch()
    ->terms('tags', ['available', 'new'])
    ->execute();
```

## Wildcard

`wildcardSearch` returns documents that [contain terms matching a wildcard pattern](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#query-dsl-wildcard-query):

```php
$searchResult = Book::wildcardSearch()
    ->field('title')
    ->value('bo*k')
    ->execute();
```

Available methods provided by `WildcardQueryBuilder`:

* [boost](#wildcard-boost)
* [field](#wildcard-field)
* [rewrite](#wildcard-rewrite)
* [value](#wildcard-value)

### <a name="wildcard-boost"></a> boost

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$searchResult = Book::wildcardSearch()
    ->field('title')
    ->value('bo*k')
    ->boost(2)
    ->execute();
```

### <a name="wildcard-field"></a> field

Use `field` to specify the [field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-top-level-params):

```php
$searchResult = Book::wildcardSearch()
    ->field('title')
    ->value('bo*k')
    ->execute();
```

### <a name="wildcard-rewrite"></a> rewrite

`rewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$searchResult = Book::wildcardSearch()
    ->field('title')
    ->value('bo*k')
    ->rewrite('constant_score')
    ->execute();
```

### <a name="wildcard-value"></a> value

With `value` you can define a [wildcard pattern for terms you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html#wildcard-query-field-params):

```php
$searchResult = Book::wildcardSearch()
    ->field('title')
    ->value('bo*k')
    ->execute();
```
