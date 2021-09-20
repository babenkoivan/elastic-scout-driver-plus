<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use stdClass;

final class MatchAllQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $type = 'match_all';

    public function buildQuery(): array
    {
        return [
            $this->type => new stdClass(),
        ];
    }
}
