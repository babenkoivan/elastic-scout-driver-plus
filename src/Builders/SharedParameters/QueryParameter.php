<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait QueryParameter
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
}
