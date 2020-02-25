<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Output;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Output\Match;
use ElasticScoutDriverPlus\Output\SearchResult;
use ElasticScoutDriverPlus\Tests\App\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Output\SearchResult
 * @uses   \ElasticScoutDriverPlus\Output\Match
 */
final class SearchResultTest extends TestCase
{
    /**
     * @var MockObject
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
            new Match($this->factory, new Document('1', ['title' => 'test 1'])),
            new Match($this->factory, new Document('2', ['title' => 'test 2'])),
        ]);

        $searchResult = new SearchResult($matches, $matches->count());

        $this->assertSame($searchResult->matches(), $matches);
    }

    public function test_models_can_be_received(): void
    {
        $models = collect([
            (new Book())->forceFill(['id' => 2, 'title' => 'test 2']),
            (new Book())->forceFill(['id' => 1, 'title' => 'test 1']),
        ]);

        $this->factory->expects($this->exactly($models->count()))
            ->method('makeById')
            ->withConsecutive(...$models->pluck('id')->map(function (int $id) {
                return Arr::wrap($id);
            }))
            ->willReturnOnConsecutiveCalls(...$models->all());

        $matches = $models->map(function (Model $model) {
            return new Match($this->factory, new Document((string)$model->getScoutKey(), $model->toSearchableArray()));
        });

        $searchResult = new SearchResult($matches, $matches->count());

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
            return new Match($this->factory, $document);
        });

        $searchResult = new SearchResult($matches, $matches->count());

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
            return new Match($this->factory, new Document((string)$index, []), $highlight);
        });

        $searchResult = new SearchResult($matches, $matches->count());

        $this->assertEquals($searchResult->highlights(), $highlights->filter()->values());
    }

    public function test_total_can_be_received(): void
    {
        $total = rand(2, 100);

        $searchResult = new SearchResult(collect(), $total);

        $this->assertSame($total, $searchResult->total());
    }
}
