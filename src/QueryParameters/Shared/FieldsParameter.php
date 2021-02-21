<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait FieldsParameter
{
    /**
     * @param string[] $fields
     */
    public function fields(array $fields): self
    {
        $this->parameters->put('fields', $fields);
        return $this;
    }
}
