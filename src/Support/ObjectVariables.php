<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ObjectVariables
{
    private function getObjectVariables(bool $snakeKeys = true): Collection
    {
        $vars = collect(get_object_vars($this));

        if ($snakeKeys) {
            return $vars->mapWithKeys(static function ($var, $key) {
                return [Str::snake($key) => $var];
            });
        }

        return $vars;
    }
}
