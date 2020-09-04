<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait LenientParameter
{
    /**
     * @var bool|null
     */
    private $lenient;

    public function lenient(bool $lenient): self
    {
        $this->lenient = $lenient;
        return $this;
    }
}
