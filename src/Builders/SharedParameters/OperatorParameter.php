<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait OperatorParameter
{
    /**
     * @var string|null
     */
    private $operator;

    public function operator(string $operator): self
    {
        $this->operator = $operator;
        return $this;
    }
}
