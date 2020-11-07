# Full Text Queries

* [Match All](#match-all)
* [Match None](#match-none)
* [Match Phrase Prefix](#match-phrase-prefix)
* [Match Phrase](#match-phrase)
* [Match](#match)
* [Multi-Match](#multi-match)

## Match All

Use `matchAllSearch` to perform a search request, which 
[matches all documents](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html#query-dsl-match-all-query):

```php
$searchResult = Book::matchAllSearch()->execute();
```

## Match None

`matchNoneSearch` is [the inverse](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html#query-dsl-match-none-query)
of [`matchAllSearch`](#match-all):

```php
$searchResult = Book::matchNoneSearch()->execute();
```

## Match Phrase Prefix

Use `matchPhrasePrefixSearch` to search for documents, that [contain the words of a provided text](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#query-dsl-match-query-phrase-prefix), 
in the same order as provided:

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->execute();
```

Available methods:

* [analyzer](#match-phrase-prefix-analyzer)
* [field](#match-phrase-prefix-field)
* [maxExpansions](#match-phrase-prefix-max-expansions)
* [query](#match-phrase-prefix-query)
* [slop](#match-phrase-prefix-slop)
* [zeroTermsQuery](#match-phrase-prefix-zero-terms-query)

### <a name="match-phrase-prefix-analyzer"></a> analyzer

`analyzer` is used to [convert the `query` text into tokens](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-field-params):

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->analyzer('english')
    ->execute();
```

### <a name="match-phrase-prefix-field"></a> field

Use `field` to specify [the field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-top-level-params):

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->execute();
```

### <a name="match-phrase-prefix-max-expansions"></a> maxExpansions

You can use `maxExpansions` to specify [maximum number of terms to which the last provided term of the `query` value will expand](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-field-params):

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->maxExpansions(50)
    ->execute();
```

### <a name="match-phrase-prefix-query"></a> query

Use `query` to set [the text you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-field-params):

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->execute();
```

### <a name="match-phrase-prefix-slop"></a> slop

Use `slop` to define [the maximum number of positions allowed between matching tokens](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-field-params):

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->slop(0)
    ->execute();
```

### <a name="match-phrase-prefix-zero-terms-query"></a> zeroTermsQuery

You can define [what to return in case `analyzer` removes all tokens](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html#match-phrase-prefix-field-params)
with `zeroTermsQuery`: 

```php
$searchResult = Book::matchPhrasePrefixSearch()
    ->field('title')
    ->query('My boo')
    ->zeroTermsQuery('none')
    ->execute();
```

## Match Phrase

Use `matchPhraseSearch` to search for documents, which [match the given phrase](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase.html#query-dsl-match-query-phrase):

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->execute();
``` 

Available methods:

* [analyzer](#match-phrase-analyzer)
* [field](#match-phrase-field)
* [query](#match-phrase-query)
* [slop](#match-phrase-slop)
* [zeroTermsQuery](#match-phrase-zero-terms-query)

### <a name="match-phrase-analyzer"></a> analyzer

`analyzer` is used to convert the `query` text into tokens:

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->analyzer('english')
    ->execute();
```

### <a name="match-phrase-field"></a> field

Use `field` to specify the field you wish to search:

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->execute();
```

### <a name="match-phrase-query"></a> query

Use `query` to set the text you wish to find in the provided `field`:

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->execute();
```

### <a name="match-phrase-slop"></a> slop

Use `slop` to define the maximum number of positions allowed between matching tokens:

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->slop(0)
    ->execute();
```

### <a name="match-phrase-zero-terms-query"></a> zeroTermsQuery

You can define what to return in case `analyzer` removes all tokens with `zeroTermsQuery`: 

```php
$searchResult = Book::matchPhraseSearch()
    ->field('title')
    ->query('My book')
    ->zeroTermsQuery('none')
    ->execute();
```

## Match

Use `matchSearch` for [full-text search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#query-dsl-match-query):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->execute();
```

Available methods:

* [analyzer](#match-analyzer)
* [autoGenerateSynonymsPhraseQuery](#match-auto-generate-synonyms-phrase-query)
* [field](#match-field)
* [fuzziness](#match-fuzziness)
* [fuzzyRewrite](#match-fuzzy-rewrite)
* [fuzzyTranspositions](#match-fuzzy-transpositions)
* [lenient](#match-lenient)
* [maxExpansions](#match-max-expansions)
* [minimumShouldMatch](#match-minimum-should-match)
* [operator](#match-operator)
* [prefixLength](#match-prefix-length)
* [query](#match-query)
* [zeroTermsQuery](#match-zero-terms-query)

### <a name="match-analyzer"></a> analyzer

`analyzer` is used to [convert the `query` text into tokens](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->analyzer('english')
    ->execute();
```

### <a name="match-auto-generate-synonyms-phrase-query"></a> autoGenerateSynonymsPhraseQuery

`autoGenerateSynonymsPhraseQuery` allows you to define, if match phrase queries have to be [automatically created
for multi-term synonyms](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->autoGenerateSynonymsPhraseQuery(true)
    ->execute();
```

### <a name="match-boost"></a> boost 

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-boost.html):

 ```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->boost(2)
    ->execute();
```

### <a name="match-field"></a> field

Use `field` to specify [the field you wish to search](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-top-level-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->execute();
```

### <a name="match-fuzziness"></a> fuzziness

`fuzziness` controls [maximum edit distance allowed for matching](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->fuzziness('AUTO')
    ->execute();
```

### <a name="match-fuzzy-rewrite"></a> fuzzyRewrite

`fuzzyRewrite` is used to [rewrite the query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->fuzzyRewrite('constant_score')
    ->execute();
```

### <a name="match-fuzzy-transpositions"></a> fuzzyTranspositions

Use `fuzzyTranspositions` to allow [transpositions for two adjacent characters](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->fuzziness('AUTO')
    ->fuzzyTranspositions(true)
    ->execute();
```

### <a name="match-lenient"></a> lenient

Use `lenient` to [ignore format-based errors](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('price')
    ->query('My book')
    ->lenient(true)
    ->execute();
```

### <a name="match-max-expansions"></a> maxExpansions

You can use `maxExpansions` to specify [maximum number of terms to which the query will expand](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->maxExpansions(50)
    ->execute();
```

### <a name="match-minimum-should-match"></a> minimumShouldMatch

`minimumShouldMatch` defines [minimum number of clauses that must match for a document to be returned](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->operator('OR')
    ->minimumShouldMatch(1)
    ->execute();
``` 

### <a name="match-operator"></a> operator

Use `operator` to define [the boolean logic used to interpret the `query` text](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->operator('OR')
    ->execute();
```

### <a name="match-prefix-length"></a> prefixLength

`prefixLength` is used to determine [the number of beginning characters left unchanged for fuzzy matching](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')
    ->fuzziness('AUTO')
    ->prefixLength(0)
    ->execute();
```

### <a name="match-query"></a> query

Use `query` to set [the text you wish to find in the provided `field`](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params):

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')  
    ->execute();
```

### <a name="match-zero-terms-query"></a> zeroTermsQuery

You can define [what to return in case `analyzer` removes all tokens](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html#match-field-params)
with `zeroTermsQuery`: 

```php
$searchResult = Book::matchSearch()
    ->field('title')
    ->query('My book')  
    ->zeroTermsQuery('none')
    ->execute();
```

## Multi-Match

Use `multiMatchSearch` to preform [full-text search in multiple fields](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html#query-dsl-multi-match-query):

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->execute();
```

Available methods:

* [analyzer](#multi-match-analyzer)
* [autoGenerateSynonymsPhraseQuery](#multi-match-auto-generate-synonyms-phrase-query)
* [boost](#multi-match-boost)
* [fields](#multi-match-fields)
* [fuzziness](#multi-match-fuzziness)
* [fuzzyRewrite](#multi-match-fuzzy-rewrite)
* [fuzzyTranspositions](#multi-match-fuzzy-transpositions)
* [lenient](#multi-match-lenient)
* [maxExpansions](#multi-match-max-expansions)
* [minimumShouldMatch](#multi-match-minimum-should-match)
* [operator](#multi-match-operator)
* [prefixLength](#multi-match-prefix-length)
* [query](#multi-match-query)
* [slop](#multi-match-phrase-slop)
* [tieBreaker](#multi-match-tie-breaker)
* [type](#multi-match-type)
* [zeroTermsQuery](#multi-match-zero-terms-query)

### <a name="multi-match-analyzer"></a> analyzer 

`analyzer` is used to convert the `query` text into tokens:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->analyzer('english')
    ->execute();
```

### <a name="multi-match-auto-generate-synonyms-phrase-query"></a> autoGenerateSynonymsPhraseQuery 

`autoGenerateSynonymsPhraseQuery` allows you to define, if match phrase queries have to be automatically created
for multi-term synonyms:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->autoGenerateSynonymsPhraseQuery(true)
    ->execute();
```

### <a name="multi-match-boost"></a> boost 

`boost` method allows you to [decrease or increase the relevance scores of a query](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-boost.html):

 ```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->boost(2)
    ->execute();
```

### <a name="multi-match-fields"></a> fields 

Use `fields` to define [the fields you wish to search in](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html#field-boost):

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->execute();
```

### <a name="multi-match-fuzziness"></a> fuzziness 

`fuzziness` controls maximum edit distance allowed for matching:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->fuzziness('AUTO')
    ->execute();
```

### <a name="multi-match-fuzzy-rewrite"></a> fuzzyRewrite 

`fuzzyRewrite` is used to rewrite the query:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->fuzzyRewrite('constant_score')
    ->execute();
```

### <a name="multi-match-fuzzy-transpositions"></a> fuzzyTranspositions 

Use `fuzzyTranspositions` to allow transpositions for two adjacent characters:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->fuzziness('AUTO')
    ->fuzzyTranspositions(true)
    ->execute();
```

### <a name="multi-match-lenient"></a> lenient 

Use `lenient` to ignore format-based errors:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->lenient(true)
    ->execute();
```

### <a name="multi-match-max-expansions"></a> maxExpansions 

You can use `maxExpansions` to specify maximum number of terms to which the query will expand:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->maxExpansions(50)
    ->execute();
```

### <a name="multi-match-minimum-should-match"></a> minimumShouldMatch 

`minimumShouldMatch` defines minimum number of clauses that must match for a document to be returned:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->operator('OR')
    ->minimumShouldMatch(1)
    ->execute();
``` 

### <a name="multi-match-operator"></a> operator 

Use `operator` to define the boolean logic used to interpret the `query` text:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->operator('OR')
    ->execute();
```

### <a name="multi-match-prefix-length"></a> prefixLength 

`prefixLength` is used to determine the number of beginning characters left unchanged for fuzzy matching:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->fuzziness('AUTO')
    ->prefixLength(0)
    ->execute();
```

### <a name="multi-match-query"></a> query 

Use `query` to set the text you wish to find in the provided `fields`:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->execute();
```

### <a name="multi-match-phrase-slop"></a> slop 

Use `slop` to define the maximum number of positions allowed between matching tokens:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->slop(0)
    ->execute();
```

### <a name="multi-match-tie-breaker"></a> tieBreaker 

`tieBreaker` is used to increase the [relevance scores](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-filter-context.html#relevance-scores)
of documents matching the query:

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->tieBreaker(0.3)
    ->execute();
```

### <a name="multi-match-type"></a> type 

Use `type` to define [how the query must be executed](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html#multi-match-types):

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->type('best_fields')
    ->execute();
``` 

**Note**, that not all the available methods make sense with each type. Read [the documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html#multi-match-types) 
carefully.

### <a name="multi-match-zero-terms-query"></a> zeroTermsQuery 

You can define what to return in case `analyzer` removes all tokens with `zeroTermsQuery`: 

```php
$searchResult = Book::multiMatchSearch()
    ->fields(['title', 'description'])
    ->query('My book')
    ->zeroTermsQuery('none')
    ->execute();
```
