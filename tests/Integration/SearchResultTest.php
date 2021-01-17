<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\Suggestion;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Author;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \ElasticScoutDriverPlus\SearchResult
 *
 * @uses   \ElasticScoutDriverPlus\Match
 */
final class SearchResultTest extends TestCase
{
    /**
     * @var LazyModelFactory&MockObject
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(LazyModelFactory::class);
    }

    public function test_matches_can_be_received(): void
    {
        $matches = collect([
            new Match($this->factory, new Hit(['_index' => 'books', '_id' => '1'])),
            new Match($this->factory, new Hit(['_index' => 'books', '_id' => '2'])),
        ]);

        $searchResult = new SearchResult($matches, collect(), collect(), $matches->count());

        $this->assertSame($searchResult->matches(), $matches);
    }

    public function test_models_can_be_received(): void
    {
        $models = collect([
            (new Author())->forceFill(['id' => 2, 'name' => 'author 2']),
            (new Author())->forceFill(['id' => 1, 'name' => 'author 1']),
        ]);

        $this->factory->expects($this->exactly($models->count()))
            ->method('makeByIndexNameAndDocumentId')
            ->withConsecutive(...$models->pluck('id')->map(static function (int $id) {
                return ['authors', $id];
            }))
            ->willReturnOnConsecutiveCalls(...$models->all());

        $matches = $models->map(function (Author $model) {
            $hit = new Hit([
                '_index' => 'authors',
                '_id' => (string)$model->getScoutKey(),
                '_source' => $model->toSearchableArray(),
            ]);

            return new Match($this->factory, $hit);
        });

        $searchResult = new SearchResult($matches, collect(), collect(), $matches->count());

        $this->assertEquals($searchResult->models()->toArray(), $models->toArray());
    }

    public function test_documents_can_be_received(): void
    {
        $documents = collect([
            new Document('1', ['title' => 'test 1']),
            new Document('2', ['title' => 'test 2']),
            new Document('3', ['title' => 'test 3']),
        ]);

        $matches = $documents->map(function (Document $document) {
            $hit = new Hit([
                '_index' => 'books',
                '_id' => $document->getId(),
                '_source' => $document->getContent(),
            ]);

            return new Match($this->factory, $hit);
        });

        $searchResult = new SearchResult($matches, collect(), collect(), $matches->count());

        $this->assertEquals($searchResult->documents(), $documents);
    }

    public function test_highlights_can_be_received(): void
    {
        $highlights = collect([
            new Highlight(['title' => '<em>foo</em>']),
            null,
            new Highlight(['title' => '<em>bar</em>']),
        ]);

        $matches = $highlights->map(function (?Highlight $highlight, int $counter) {
            $hit = new Hit([
                '_index' => 'books',
                '_id' => (string)$counter,
                'highlight' => $highlight ? $highlight->getRaw() : null,
            ]);

            return new Match($this->factory, $hit);
        });

        $searchResult = new SearchResult($matches, collect(), collect(), $matches->count());

        $this->assertEquals($searchResult->highlights(), $highlights->filter()->values());
    }

    public function test_total_can_be_received(): void
    {
        $total = rand(2, 100);

        $searchResult = new SearchResult(collect(), collect(), collect(), $total);

        $this->assertSame($total, $searchResult->total());
    }

    public function test_suggestions_can_be_received(): void
    {
        $suggestions = collect([
            'title' => collect([
                new Suggestion(['text' => 'foo', 'offset' => 0, 'length' => 3, 'options' => []]),
                new Suggestion(['text' => 'bar', 'offset' => 4, 'length' => 3, 'options' => []]),
            ]),
        ]);

        $searchResult = new SearchResult(collect(), $suggestions, collect(), null);

        $this->assertSame($suggestions, $searchResult->suggestions());
    }

    public function test_aggregations_can_be_received(): void
    {
        $aggregations = collect([
            'max_price' => [
                'value' => 100,
            ],
        ]);

        $searchResult = new SearchResult(collect(), collect(), $aggregations, null);

        $this->assertSame($aggregations, $searchResult->aggregations());
    }
}
