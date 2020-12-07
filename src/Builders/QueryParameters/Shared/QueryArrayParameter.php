<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Shared;

use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use ElasticScoutDriverPlus\Builders\QueryParameters\Factory;

trait QueryArrayParameter
{
    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public function query($type, array $query = []): self
    {
        $this->parameters->put('query', Factory::makeQuery($type, $query));
        return $this;
    }
}
