<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

use Closure;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use function ElasticScoutDriverPlus\query;

trait QueryParameter
{
    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function query($query): self
    {
        $this->parameters->put('query', query($query));
        return $this;
    }
}
