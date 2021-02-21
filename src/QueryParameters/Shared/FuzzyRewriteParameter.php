<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait FuzzyRewriteParameter
{
    public function fuzzyRewrite(string $fuzzyRewrite): self
    {
        $this->parameters->put('fuzzy_rewrite', $fuzzyRewrite);
        return $this;
    }
}
