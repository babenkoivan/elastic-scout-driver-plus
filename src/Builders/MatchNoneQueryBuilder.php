<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use stdClass;

final class MatchNoneQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $type = 'match_none';

    public function buildQuery(): array
    {
        return [
            $this->type => new stdClass(),
        ];
    }
}
