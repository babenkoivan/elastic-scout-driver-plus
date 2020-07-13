<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class RawQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array|null
     */
    private $query;

    public function query(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function buildQuery(): array
    {
        if (is_null($this->query)) {
            throw new QueryBuilderException('Query is not specified');
        }

        return $this->query;
    }
}
