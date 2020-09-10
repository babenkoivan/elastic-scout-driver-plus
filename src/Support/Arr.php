<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Support;

use Illuminate\Support\Arr as BaseArr;

final class Arr extends BaseArr
{
    public static function wrapAssoc(array $array): array
    {
        if (!self::isAssoc($array)) {
            return $array;
        }

        return array_map(static function ($value, $key) {
            return [$key => $value];
        }, $array, array_keys($array));
    }
}
