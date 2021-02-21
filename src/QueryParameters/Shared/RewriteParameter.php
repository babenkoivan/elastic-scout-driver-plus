<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

trait RewriteParameter
{
    public function rewrite(string $rewrite): self
    {
        $this->parameters->put('rewrite', $rewrite);
        return $this;
    }
}
