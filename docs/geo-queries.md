# Geo Queries

* [Geo-Distance](#geo-distance)
* [Geo-Shape](#geo-shape)

## Geo-Distance

You can use `Elastic\ScoutDriverPlus\Support\Query::geoDistance()` to build a [geo-distance query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#query-dsl-geo-distance-query):

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

## Geo-Shape

You can use `Elastic\ScoutDriverPlus\Support\Query::geoShape()` to build a [geo-shape query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html#query-dsl-geo-shape-query):

```php
$query = Query::geoShape()
    ->field('location')
    ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
    ->relation('within');

$searchResult = Store::searchQuery($query)->execute();
```

Available methods:

* [field](#geo-shape-field)
* [relation](#geo-shape-relation)
* [shape](#geo-shape-shape)
* [ignoreUnmapped](#geo-shape-ignore-unmapped)

### <a name="geo-shape-field"></a> field

Use `field` to specify the field, which represents a geo field:

```php
$query = Query::geoShape()
    ->field('location')
    ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
    ->relation('within');

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-shape-relation"></a> relation

`relation` [defines a spatial relation](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html#geo-shape-spatial-relations) when searching a geo field:

```php
$query = Query::geoShape()
    ->field('location')
    ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
    ->relation('within');

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-shape-shape"></a> shape

Use `shape` to define a [GeoJSON](https://geojson.org) representation of a shape:

```php
$query = Query::geoShape()
    ->field('location')
    ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
    ->relation('within');

$searchResult = Store::searchQuery($query)->execute();
```

### <a name="geo-shape-ignore-unmapped"></a> ignoreUnmapped

You can use `ignoreUnmapped` to query [multiple indexes which might have different mappings](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html#_ignore_unmapped_4):

```php
$query = Query::geoShape()
    ->field('location')
    ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
    ->relation('within')
    ->ignoreUnmapped(true);

$searchResult = Store::searchQuery($query)->execute();
```
