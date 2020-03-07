<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Scopes;

use ElasticScoutDriverPlus\Builders\RawSearchRequestBuilder;

trait RawSearchScope
{
    public static function rawSearchQuery(): RawSearchRequestBuilder
    {
        return new RawSearchRequestBuilder(new static());
    }
}
