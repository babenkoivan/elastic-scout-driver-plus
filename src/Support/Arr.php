<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Support;

use Illuminate\Support\Arr as BaseArr;

final class Arr extends BaseArr
{
    /**
     * Wrap each key / value pair of the given assoc array in an array.
     * If the given array is not an associative array, then it will not be changed.
     */
    public static function wrapAssocArray(array $array): array
    {
        if (!static::isAssoc($array)) {
            return $array;
        }

        return array_map(static function ($value, $key) {
            return [$key => $value];
        }, $array, array_keys($array));
    }
}
