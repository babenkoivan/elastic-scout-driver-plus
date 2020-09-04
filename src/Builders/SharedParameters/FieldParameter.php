<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait FieldParameter
{
    /**
     * @var string|null
     */
    private $field;

    public function field(string $field): self
    {
        $this->field = $field;
        return $this;
    }
}
