<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters;

use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use stdClass;

final class Factory
{
    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public static function makeQuery($type, array $query = []): array
    {
        if (is_string($type)) {
            return [
                $type => empty($query) ? new stdClass() : $query,
            ];
        }

        return $type instanceof QueryBuilderInterface ? $type->buildQuery() : $type;
    }
}
