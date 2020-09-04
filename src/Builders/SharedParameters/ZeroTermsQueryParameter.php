<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait ZeroTermsQueryParameter
{
    /**
     * @var string|null
     */
    private $zeroTermsQuery;

    public function zeroTermsQuery(string $zeroTermsQuery): self
    {
        $this->zeroTermsQuery = $zeroTermsQuery;
        return $this;
    }
}
