<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use Closure;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;

/**
 * @param Closure|QueryBuilderInterface|array
 */
function query($query): array
{
    $query = value($query);

    return $query instanceof QueryBuilderInterface ? $query->buildQuery() : $query;
}
