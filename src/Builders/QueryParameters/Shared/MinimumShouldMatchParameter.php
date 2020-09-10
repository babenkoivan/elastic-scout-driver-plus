<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

trait MinimumShouldMatchParameter
{
    /**
     * @param int|string $minimumShouldMatch
     */
    public function minimumShouldMatch($minimumShouldMatch): self
    {
        $this->parameters->put('minimum_should_match', $minimumShouldMatch);
        return $this;
    }
}
