<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait RewriteParameter
{
    public function rewrite(string $rewrite): self
    {
        $this->parameters->put('rewrite', $rewrite);
        return $this;
    }
}
