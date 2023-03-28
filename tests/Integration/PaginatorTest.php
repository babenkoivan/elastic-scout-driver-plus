<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration;

use Elastic\Adapter\Documents\Document;
use Elastic\Adapter\Search\SearchResult as BaseSearchResult;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Paginator;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\App\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @covers \Elastic\ScoutDriverPlus\Paginator
 *
 * @uses   \Elastic\ScoutDriverPlus\Decorators\Hit
 * @uses   \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses   \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class PaginatorTest extends TestCase
{
    private Paginator $paginator;

    protected function setUp(): void
    {
        parent::setUp();

        $baseSearchResult = new BaseSearchResult([
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

        $modelFactory = $this->createMock(ModelFactory::class);

        $modelFactory->expects($this->any())
            ->method('makeFromIndexNameAndDocumentIds')
            ->with('test', [(string)$model->getScoutKey()])
            ->willReturn(new Collection([$model]));

        $searchResult = new SearchResult($baseSearchResult, $modelFactory);
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
