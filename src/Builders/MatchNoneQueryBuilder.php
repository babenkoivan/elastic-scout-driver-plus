<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use stdClass;

final class MatchNoneQueryBuilder implements QueryBuilderInterface
{
    protected string $type = 'match_none';

    public function buildQuery(): array
    {
        return [
            $this->type => new stdClass(),
        ];
    }
}
