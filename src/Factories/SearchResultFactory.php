<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class SearchResultFactory
{
    public function makeFromSearchResponseForModel(SearchResponse $searchResponse, Model $model): SearchResult
    {
        $matches = $this->makeMatches(
            $searchResponse->getHits(),
            new LazyModelFactory($model, $searchResponse)
        );

        $suggestions = $this->makeSuggestions($searchResponse->getSuggestions());

        return new SearchResult($matches, $searchResponse->getHitsTotal(), $suggestions);
    }

    private function makeMatches(array $hits, LazyModelFactory $lazyModelFactory): Collection {
        return collect($hits)->map(function (Hit $hit) use ($lazyModelFactory) {
            return new Match($lazyModelFactory, $hit->getDocument(), $hit->getHighlight());
        });
    }

    private function makeSuggestions(array $suggestions): Collection
    {
        return collect($suggestions)->mapWithKeys(function (array $entries, string $suggestion) {
            return [$suggestion => collect($entries)];
        });
    }
}
