<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait TypeParameter
{
    /**
     * @var string|null
     */
    private $type;

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
