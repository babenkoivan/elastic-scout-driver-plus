<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\QueryParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class RawQueryBuilder implements QueryBuilderInterface
{
    use QueryParameter;

    public function buildQuery(): array
    {
        if (is_null($this->query)) {
            throw new QueryBuilderException('Query is not specified');
        }

        return $this->query;
    }
}
