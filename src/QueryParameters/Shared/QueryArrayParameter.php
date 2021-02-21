<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use ElasticScoutDriverPlus\QueryParameters\Factory;

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
