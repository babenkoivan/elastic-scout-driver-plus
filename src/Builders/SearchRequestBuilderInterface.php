<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\SearchResult;

interface SearchRequestBuilderInterface
{
    public function buildSearchRequest(): SearchRequest;

    public function execute(): SearchResult;

    public function raw(): array;
}
