<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Exceptions\SearchRequestBuilderException;

final class RawSearchRequestBuilder extends AbstractSearchRequestBuilder
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

    protected function buildQuery(): array
    {
        if (is_null($this->query)) {
            throw new SearchRequestBuilderException('Query is not specified');
        }

        return $this->query;
    }
}
