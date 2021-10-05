<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait ValueParameter
{
    /**
     * @param int|float|bool|string $value
     * @return $this
     */
    public function value($value): self
    {
        $this->parameters->put('value', $value);
        return $this;
    }
}
