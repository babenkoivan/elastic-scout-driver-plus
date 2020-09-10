<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use stdClass;

final class MatchAllQueryBuilder implements QueryBuilderInterface
{
    public function buildQuery(): array
    {
        return [
            'match_all' => new stdClass(),
        ];
    }
}
