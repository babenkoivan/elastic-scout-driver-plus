<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Scopes;

use ElasticScoutDriverPlus\Builders\BoolSearchRequestBuilder;

trait BoolSearchScope
{
    public static function boolSearchQuery(): BoolSearchRequestBuilder
    {
        return new BoolSearchRequestBuilder(new static());
    }
}
