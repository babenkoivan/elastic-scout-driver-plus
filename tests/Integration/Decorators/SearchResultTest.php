<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Decorators;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Decorators\Hit;
use ElasticScoutDriverPlus\Decorators\SearchResult;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\App\Model;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Decorators\SearchResult
 *
 * @uses   \ElasticScoutDriverPlus\Decorators\Hit
 */
final class SearchResultTest extends TestCase
{
    /**
     * @var SearchResult
     */
    private $searchResult;

    protected function setUp(): void
    {
        parent::setUp();

        $searchResponse = new SearchResponse([
            'hits' => [
                'hits' => [
                    [
                        '_id' => '1',
                        '_index' => 'test',
                        '_source' => ['title' => 'foo'],
                        '_score' => 1.1,
                        'highlight' => ['title' => [' <em>foo</em> ']],
                    ],
                ],
            ],
        ]);

        $model = new Book([
            'id' => 1,
            'title' => 'foo',
        ]);

        $lazyModelFactory = $this->createMock(LazyModelFactory::class);

        $lazyModelFactory->expects($this->any())
            ->method('makeByIndexNameAndDocumentId')
            ->with('test', '1')
            ->willReturn($model);

        $this->searchResult = new SearchResult($searchResponse, $lazyModelFactory);
    }

    public function test_hits_can_be_retrieved(): void
    {
        $hits = $this->searchResult->hits();

        $this->assertCount(1, $hits);
        $this->assertInstanceOf(Hit::class, $hits->first());
        $this->assertSame('test', $hits->first()->indexName());
    }

    public function test_models_can_be_retrieved(): void
    {
        $models = $this->searchResult->models();

        $this->assertCount(1, $models);
        $this->assertInstanceOf(Model::class, $models->first());
        $this->assertSame(1, $models->first()->id);
    }

    public function test_documents_can_be_retrieved(): void
    {
        $documents = $this->searchResult->documents();

        $this->assertCount(1, $documents);
        $this->assertInstanceOf(Document::class, $documents->first());
        $this->assertSame('1', $documents->first()->id());
    }

    public function test_highlights_can_be_retrieved(): void
    {
        $highlights = $this->searchResult->highlights();

        $this->assertCount(1, $highlights);
        $this->assertInstanceOf(Highlight::class, $highlights->first());
        $this->assertSame(' <em>foo</em> ', $highlights->first()->snippets('title')->first());
    }

    public function test_results_can_be_iterated(): void
    {
        foreach ($this->searchResult as $hit) {
            $this->assertInstanceOf(Hit::class, $hit);
            $this->assertSame('test', $hit->indexName());
        }
    }
}
