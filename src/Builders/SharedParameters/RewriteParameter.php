<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait RewriteParameter
{
    /**
     * @var string|null
     */
    private $rewrite;

    public function rewrite(string $rewrite): self
    {
        $this->rewrite = $rewrite;
        return $this;
    }
}
