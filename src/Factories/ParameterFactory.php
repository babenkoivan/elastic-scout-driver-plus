<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use Closure;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;

class ParameterFactory
{
    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public static function makeQuery($query): array
    {
        $query = value($query);

        return $query instanceof QueryBuilderInterface ? $query->buildQuery() : $query;
    }
}
