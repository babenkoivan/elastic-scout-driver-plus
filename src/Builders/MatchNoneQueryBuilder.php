<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use stdClass;

final class MatchNoneQueryBuilder implements QueryBuilderInterface
{
    public function buildQuery(): array
    {
        return [
            'match_none' => new stdClass(),
        ];
    }
}
