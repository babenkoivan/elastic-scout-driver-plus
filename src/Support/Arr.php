<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Support;

use Illuminate\Support\Arr as BaseArr;

final class Arr extends BaseArr
{
    public static function wrapAssoc(array $array): array
    {
        if (!self::isAssoc($array)) {
            return $array;
        }

        return array_map(static fn ($value, $key) => [$key => $value], $array, array_keys($array));
    }
}
