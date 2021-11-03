<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\SearchResponse;
use ElasticScoutDriverPlus\Decorators\Hit;
use ElasticScoutDriverPlus\Decorators\SearchResult;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Paginator;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\App\Model;

/**
 * @covers \ElasticScoutDriverPlus\Paginator
 *
 * @uses   \ElasticScoutDriverPlus\Decorators\Hit
 * @uses   \ElasticScoutDriverPlus\Decorators\SearchResult
 */
final class PaginatorTest extends TestCase
{
    /**
     * @var Paginator
     */
    private $paginator;

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
                'total' => [
                    'value' => 1,
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

        $searchResult = new SearchResult($searchResponse, $lazyModelFactory);
        $this->paginator = new Paginator($searchResult, 1);
    }

    public function test_only_models_can_be_paginated(): void
    {
        $models = $this->paginator->onlyModels();

        $this->assertCount(1, $models);
        $this->assertInstanceOf(Model::class, $models->first());
        $this->assertSame(1, $models->first()->id);
    }

    public function test_only_documents_can_be_paginated(): void
    {
        $documents = $this->paginator->onlyDocuments();

        $this->assertCount(1, $documents);
        $this->assertInstanceOf(Document::class, $documents->first());
        $this->assertSame('1', $documents->first()->id());
    }

    public function test_call_forwarding(): void
    {
        $this->assertInstanceOf(Hit::class, $this->paginator->first());
        $this->assertInstanceOf(Model::class, $this->paginator->models()->first());
    }
}
