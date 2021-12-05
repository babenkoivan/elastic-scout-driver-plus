<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Shared;

use stdClass;

trait InnerHitsParameter
{
    public function innerHits(?array $options = null): self
    {
        $this->parameters->put('inner_hits', $options ?? new stdClass());

        return $this;
    }
}
