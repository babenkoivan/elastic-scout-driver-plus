<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait CaseInsensitiveParameter
{
    public function caseInsensitive(bool $caseInsensitive): self
    {
        $this->parameters->put('case_insensitive', $caseInsensitive);
        return $this;
    }
}
