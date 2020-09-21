<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\SearchResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SearchRequestBuilderInterface
{
    public const DEFAULT_PAGE_SIZE = 10;

    public function buildSearchRequest(): SearchRequest;

    public function execute(): SearchResult;

    public function raw(): array;

    public function paginate(
        int $perPage = self::DEFAULT_PAGE_SIZE,
        string $pageName = 'page',
        int $page = null
    ): LengthAwarePaginator;
}
