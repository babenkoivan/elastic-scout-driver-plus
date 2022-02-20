# Geo Queries

* [Geo-Distance](#geo-distance)

## Geo-Distance

You can use `ElasticScoutDriverPlus\Support\Query::geoDistance()` to build a [geo-distance query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#query-dsl-geo-distance-query):

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70);

$searchResult = Store::searchQuery($query)->execute();
```

Available methods:

* [distanceType](#geo-distance-distance-type)
* [distance](#geo-distance-distance)
* [field](#geo-distance-field)
* [ignoreUnmapped](#geo-distance-ignore-unmapped)
* [lat](#geo-distance-lat)
* [lon](#geo-distance-lon)
* [validationMethod](#geo-distance-validation-method)

### <a name="geo-distance-distance-type"></a> distanceType

`distanceType` defines [how to compute the distance](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#_options_2):

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70)
    ->distanceType('plane');

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-distance"></a> distance

Use `distance` to set [the radius of the circle centred on the specified location](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#_options_2):

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70);

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-field"></a> field

Use `field` to specify the field, which represents the geo point:

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70);

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-ignore-unmapped"></a> ignoreUnmapped

You can use `ignoreUnmapped` to query [multiple indexes which might have different mappings](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#_ignore_unmapped_2):

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70)
    ->ignoreUnmapped(true);

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-lat"></a> lat

`lat` defines the geo point latitude:

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70);

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-lon"></a> lon

`lon` defines the geo point longitude:

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70);

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-distance-validation-method"></a> validationMethod

`validationMethod` defines [how latitude and longitude are validated](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#_options_2):

```php
$query = Query::geoDistance()
    ->field('location')
    ->distance('200km')
    ->lat(40)
    ->lon(-70)
    ->validationMethod('IGNORE_MALFORMED');

$searchResult = Store::searchQuery($query)->execute();
```
