<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait TextParameter
{
    /**
     * @var string|null
     */
    private $text;

    public function text(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}
