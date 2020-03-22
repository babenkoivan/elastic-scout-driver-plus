<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus;

use ElasticScoutDriverPlus\Builders\BoolSearchRequestBuilder;
use ElasticScoutDriverPlus\Builders\RawSearchRequestBuilder;

trait CustomSearch
{
    public static function boolSearch(): BoolSearchRequestBuilder
    {
        return new BoolSearchRequestBuilder(new static());
    }

    public static function rawSearch(): RawSearchRequestBuilder
    {
        return new RawSearchRequestBuilder(new static());
    }
}
