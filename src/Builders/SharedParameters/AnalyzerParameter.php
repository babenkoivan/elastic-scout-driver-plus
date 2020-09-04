<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait AnalyzerParameter
{
    /**
     * @var string|null
     */
    private $analyzer;

    public function analyzer(string $analyzer): self
    {
        $this->analyzer = $analyzer;
        return $this;
    }
}
