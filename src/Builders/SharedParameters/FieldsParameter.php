<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait FieldsParameter
{
    /**
     * @var array|null
     */
    private $fields;

    /**
     * @param string[] $fields
     */
    public function fields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }
}
