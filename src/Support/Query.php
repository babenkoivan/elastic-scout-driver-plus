<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Support;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\ExistsQueryBuilder;
use ElasticScoutDriverPlus\Builders\FuzzyQueryBuilder;
use ElasticScoutDriverPlus\Builders\GeoDistanceQueryBuilder;
use ElasticScoutDriverPlus\Builders\IdsQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchAllQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhrasePrefixQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\MultiMatchQueryBuilder;
use ElasticScoutDriverPlus\Builders\NestedQueryBuilder;
use ElasticScoutDriverPlus\Builders\PrefixQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Builders\RegexpQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermsQueryBuilder;
use ElasticScoutDriverPlus\Builders\WildcardQueryBuilder;
use Illuminate\Support\Traits\Macroable;

class Query
{
    use Macroable;

    public static function bool(): BoolQueryBuilder
    {
        return new BoolQueryBuilder();
    }

    public static function nested(): NestedQueryBuilder
    {
        return new NestedQueryBuilder();
    }

    public static function matchAll(): MatchAllQueryBuilder
    {
        return new MatchAllQueryBuilder();
    }

    public static function matchNone(): MatchNoneQueryBuilder
    {
        return new MatchNoneQueryBuilder();
    }

    public static function match(): MatchQueryBuilder
    {
        return new MatchQueryBuilder();
    }

    public static function matchPhrase(): MatchPhraseQueryBuilder
    {
        return new MatchPhraseQueryBuilder();
    }

    public static function matchPhrasePrefix(): MatchPhrasePrefixQueryBuilder
    {
        return new MatchPhrasePrefixQueryBuilder();
    }

    public static function multiMatch(): MultiMatchQueryBuilder
    {
        return new MultiMatchQueryBuilder();
    }

    public static function exists(): ExistsQueryBuilder
    {
        return new ExistsQueryBuilder();
    }

    public static function fuzzy(): FuzzyQueryBuilder
    {
        return new FuzzyQueryBuilder();
    }

    public static function ids(): IdsQueryBuilder
    {
        return new IdsQueryBuilder();
    }

    public static function prefix(): PrefixQueryBuilder
    {
        return new PrefixQueryBuilder();
    }

    public static function range(): RangeQueryBuilder
    {
        return new RangeQueryBuilder();
    }

    public static function regexp(): RegexpQueryBuilder
    {
        return new RegexpQueryBuilder();
    }

    public static function term(): TermQueryBuilder
    {
        return new TermQueryBuilder();
    }

    public static function terms(): TermsQueryBuilder
    {
        return new TermsQueryBuilder();
    }

    public static function wildcard(): WildcardQueryBuilder
    {
        return new WildcardQueryBuilder();
    }

    public static function geoDistance(): GeoDistanceQueryBuilder
    {
        return new GeoDistanceQueryBuilder();
    }
}
