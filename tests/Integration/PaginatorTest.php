<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Hit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\Paginator;
use ElasticScoutDriverPlus\SearchResult;
use RuntimeException;

/**
 * @covers \ElasticScoutDriverPlus\Paginator
 *
 * @uses \ElasticScoutDriverPlus\Match
 * @uses \ElasticScoutDriverPlus\SearchResult
 */
final class PaginatorTest extends TestCase
{
    public function test_forwards_calls_to_search_result(): void
    {
        $factory = $this->createMock(LazyModelFactory::class);

        $documents = collect([
            new Document('1', ['title' => 'test 1']),
            new Document('2', ['title' => 'test 2']),
        ]);

        $matches = $documents->map(static function (Document $document) use ($factory) {
            $hit = new Hit([
                '_index' => 'books',
                '_id' => $document->getId(),
                '_source' => $document->getContent(),
            ]);

            return new Match($factory, $hit);
        });

        $searchResult = new SearchResult($matches, collect(), collect(), $matches->count());
        $paginator = new Paginator($searchResult, 2, 1);

        $this->assertEquals($matches, $paginator->matches());
        $this->assertEquals($documents, $paginator->documents());
    }

    public function test_exception_is_thrown_when_search_result_has_no_total_value(): void
    {
        $this->expectException(RuntimeException::class);

        $searchResult = new SearchResult(collect(), collect(), collect(), null);
        new Paginator($searchResult, 10);
    }
}
