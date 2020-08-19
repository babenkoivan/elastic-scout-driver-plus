<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Suggestion;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Book;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
            new Match($this->factory, 'books', new Document('1', ['title' => 'test 1']), null, null),
            new Match($this->factory, 'books', new Document('2', ['title' => 'test 2']), null, null),
        ]);

        $searchResult = new SearchResult($matches, $matches->count(), collect(), collect());

        $this->assertSame($searchResult->matches(), $matches);
    }

    public function test_models_can_be_received(): void
    {
        $models = collect([
            (new Book())->forceFill(['id' => 2, 'title' => 'test 2']),
            (new Book())->forceFill(['id' => 1, 'title' => 'test 1']),
        ]);

        $this->factory->expects($this->exactly($models->count()))
            ->method('makeByIndexNameAndDocumentId')
            ->withConsecutive(...$models->pluck('id')->map(static function (int $id) {
                return ['books', $id];
            }))
            ->willReturnOnConsecutiveCalls(...$models->all());

        $matches = $models->map(function (Book $model) {
            $document = new Document((string)$model->getScoutKey(), $model->toSearchableArray());
            return new Match($this->factory, 'books', $document, null, null);
        });

        $searchResult = new SearchResult($matches, $matches->count(), collect(), collect());

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
            return new Match($this->factory, 'books', $document, null, null);
        });

        $searchResult = new SearchResult($matches, $matches->count(), collect(), collect());

        $this->assertEquals($searchResult->documents(), $documents);
    }

    public function test_highlights_can_be_received(): void
    {
        $highlights = collect([
            new Highlight(['title' => '<em>foo</em>']),
            null,
            new Highlight(['title' => '<em>bar</em>']),
        ]);

        $matches = $highlights->map(function (?Highlight $highlight, int $index) {
            $document = new Document((string)$index, []);
            return new Match($this->factory, 'books', $document, $highlight, null);
        });

        $searchResult = new SearchResult($matches, $matches->count(), collect(), collect());

        $this->assertEquals($searchResult->highlights(), $highlights->filter()->values());
    }

    public function test_total_can_be_received(): void
    {
        $total = rand(2, 100);

        $searchResult = new SearchResult(collect(), $total, collect(), collect());

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

        $searchResult = new SearchResult(collect(), 0, $suggestions, collect());

        $this->assertSame($suggestions, $searchResult->suggestions());
    }

    public function test_aggregations_can_be_received(): void
    {
        $aggregations = collect([
            'max_price' => [
                'value' => 100,
            ],
        ]);

        $searchResult = new SearchResult(collect(), 0, collect(), $aggregations);

        $this->assertSame($aggregations, $searchResult->aggregations());
    }
}
