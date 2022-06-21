<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

use Closure;
use Elastic\ScoutDriverPlus\Builders\QueryBuilderInterface;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;

trait QueryParameter
{
    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function query($query): self
    {
        $this->parameters->put('query', ParameterFactory::makeQuery($query));
        return $this;
    }
}
