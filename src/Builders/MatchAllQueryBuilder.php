<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\BoostParameter;
use stdClass;

final class MatchAllQueryBuilder implements QueryBuilderInterface
{
    use BoostParameter;

    public function buildQuery(): array
    {
        return [
            'match_all' => isset($this->boost) ? ['boost' => $this->boost] : new stdClass(),
        ];
    }
}
