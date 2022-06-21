<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Support;

use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\ExistsQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\FuzzyQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\GeoDistanceQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\IdsQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchAllQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchNoneQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchPhrasePrefixQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MultiMatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\NestedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\PrefixQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\RangeQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\RegexpQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\TermQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\TermsQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\WildcardQueryBuilder;
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
