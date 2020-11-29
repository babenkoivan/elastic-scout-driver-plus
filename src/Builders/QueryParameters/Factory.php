<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters;

use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use stdClass;

final class Factory
{
    public static function makeQuery(array $arguments): array
    {
        // If the first argument is string, then we assume, that query type/body is used,
        // i.e. ['match', ['title' => 'My Title']] or ['match_all']
        if (is_string($arguments[0])) {
            return [
                $arguments[0] => empty($arguments[1]) ? new stdClass() : $arguments[1],
            ];
        }

        // Otherwise, the first argument is either query builder or an array
        return $arguments[0] instanceof QueryBuilderInterface ? $arguments[0]->buildQuery() : $arguments[0];
    }
}
