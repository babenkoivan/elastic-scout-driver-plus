<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration;

use Elastic\Adapter\Documents\DocumentManager;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Search\SearchResult;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\NullEngine;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\LazyCollection;
use Laravel\Scout\Builder;

/**
 * @covers \Elastic\ScoutDriverPlus\NullEngine
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @uses   \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class NullEngineTest extends TestCase
{
    private Engine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        // set driver to null
        $this->app['config']->set('scout.driver', null);

        // resolve engine instance
        $this->engine = (new Author())->searchableUsing();
        $this->assertInstanceOf(NullEngine::class, $this->engine);

        // assert that no document/index manager methods are called
        $documentManagerMock = $this->createMock(DocumentManager::class);
        $documentManagerMock->expects($this->never())->method($this->anything());
        $this->app->instance(DocumentManager::class, $documentManagerMock);

        $indexManagerMock = $this->createMock(IndexManager::class);
        $indexManagerMock->expects($this->never())->method($this->anything());
        $this->app->instance(IndexManager::class, $indexManagerMock);
    }

    public function test_update_does_nothing(): void
    {
        $models = factory(Author::class, random_int(2, 5))->create();
        $this->engine->update($models);
    }

    public function test_delete_does_nothing(): void
    {
        $models = factory(Author::class, random_int(2, 5))->create();
        $this->engine->delete($models);
    }

    public function test_search_returns_empty_result(): void
    {
        $model = factory(Author::class)->create();
        $searchResult = $this->engine->search(new Builder($model, $model->name));

        $this->assertCount(0, $searchResult->hits());
        $this->assertSame(0, $searchResult->total());
    }

    public function test_paginate_returns_empty_result(): void
    {
        $model = factory(Author::class)->create();
        $searchResult = $this->engine->paginate(new Builder($model, $model->name), 1, 1);

        $this->assertCount(0, $searchResult->hits());
        $this->assertSame(0, $searchResult->total());
    }

    public function test_map_returns_empty_collection(): void
    {
        $model = factory(Author::class)->create();
        $builder = new Builder($model, $model->name);

        $searchResult = new SearchResult([
            'hits' => [
                'total' => [
                    'value' => 1,
                ],
                'hits' => [
                    [
                        '_id' => $model->getScoutKey(),
                        '_index' => $model->searchableAs(),
                        '_source' => $model->toSearchableArray(),
                        '_score' => 1.0,
                    ],
                ],
            ],
        ]);

        $mapResult = $this->engine->map($builder, $searchResult, $model);

        $this->assertInstanceOf(EloquentCollection::class, $mapResult);
        $this->assertCount(0, $mapResult);
    }

    public function test_lazy_map_returns_empty_lazy_collection(): void
    {
        $model = factory(Author::class)->create();
        $builder = new Builder($model, $model->name);

        $searchResult = new SearchResult([
            'hits' => [
                'total' => [
                    'value' => 1,
                ],
                'hits' => [
                    [
                        '_id' => $model->getScoutKey(),
                        '_index' => $model->searchableAs(),
                        '_source' => $model->toSearchableArray(),
                        '_score' => 1.0,
                    ],
                ],
            ],
        ]);

        $lazyMapResult = $this->engine->lazyMap($builder, $searchResult, $model);

        $this->assertInstanceOf(LazyCollection::class, $lazyMapResult);
        $this->assertCount(0, $lazyMapResult);
    }

    public function test_flush_does_nothing(): void
    {
        $model = factory(Author::class)->create();
        $this->engine->flush($model);
    }

    public function test_create_index_does_nothing(): void
    {
        $this->engine->createIndex('test');
    }

    public function test_delete_index_does_nothing(): void
    {
        $this->engine->deleteIndex('test');
    }

    public function test_search_with_parameters_returns_empty_result(): void
    {
        $model = factory(Author::class)->create();
        $searchParameters = (new SearchParametersBuilder($model))->buildSearchParameters();
        $searchResult = $this->engine->searchWithParameters($searchParameters);

        $this->assertCount(0, $searchResult->hits());
        $this->assertSame(0, $searchResult->total());
    }

    public function test_connection_index_does_nothing(): void
    {
        $this->assertSame($this->engine, $this->engine->connection('test-connection'));
    }

    public function test_open_point_in_time_does_nothing(): void
    {
        $this->assertSame('', $this->engine->openPointInTime('test-index'));
    }

    public function test_close_point_in_time_does_nothing(): void
    {
        $this->engine->closePointInTime('test-pit');
    }
}
