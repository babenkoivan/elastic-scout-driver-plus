<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\QueryMatch;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Support\ModelScope;
use Illuminate\Support\Collection;

final class SearchResultFactory
{
    public static function makeFromSearchResponseUsingModelScope(
        SearchResponse $searchResponse,
        ModelScope $modelScope
    ): SearchResult {
        return new SearchResult(
            self::makeMatches($searchResponse, $modelScope),
            $searchResponse->suggestions(),
            $searchResponse->aggregations(),
            $searchResponse->total()
        );
    }

    private static function makeMatches(SearchResponse $searchResponse, ModelScope $modelScope): Collection
    {
        $lazyModelFactory = new LazyModelFactory($searchResponse, $modelScope);

        return collect($searchResponse->hits())->map(static function (Hit $hit) use ($lazyModelFactory) {
            return new QueryMatch($lazyModelFactory, $hit);
        });
    }
}
