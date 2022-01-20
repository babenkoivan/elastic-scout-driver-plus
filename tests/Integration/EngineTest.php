<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchRequest;
use ElasticScoutDriverPlus\Tests\App\Book;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Engine
 * @covers \ElasticScoutDriverPlus\Jobs\RemoveFromSearch
 *
 * @uses   \ElasticScoutDriverPlus\Factories\DocumentFactory
 * @uses   \ElasticScoutDriverPlus\Factories\RoutingFactory
 * @uses   \ElasticScoutDriverPlus\Searchable
 */
final class EngineTest extends TestCase
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManager = resolve(DocumentManager::class);
    }

    public function queueConfigProvider(): array
    {
        return [
            [['scout.queue' => true]],
            [['scout.queue' => false]],
        ];
    }

    /**
     * @dataProvider queueConfigProvider
     */
    public function test_models_can_be_indexed(array $config): void
    {
        config($config);

        $models = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        // find all indexed models
        $searchResponse = $this->documentManager->search(
            $models->first()->searchableAs(),
            (new SearchRequest(['match_all' => new stdClass()]))->sort(['id'])
        );

        // assert that documents have the same ids as created models
        $modelIds = $models->pluck($models->first()->getKeyName())->all();

        $documentIds = collect($searchResponse->hits())->map(static function (Hit $hit) {
            return $hit->document()->id();
        })->all();

        $this->assertEquals($modelIds, $documentIds);
    }

    /**
     * @dataProvider queueConfigProvider
     */
    public function test_models_can_be_deleted(array $config): void
    {
        config($config);

        $models = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        // delete newly created models
        $models->each(static function (Book $model) {
            $model->delete();
        });

        // find all indexed models
        $searchResponse = $this->documentManager->search(
            $models->first()->searchableAs(),
            new SearchRequest(['match_all' => new stdClass()])
        );

        // assert that there is no documents in the index
        $this->assertSame(0, $searchResponse->total());
    }

    public function test_models_can_be_found_using_default_search(): void
    {
        factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        $target = factory(Book::class)->state('belongs_to_author')->create(['title' => uniqid('test')]);
        $found = Book::search($target->title)->orderBy('id')->get();

        $this->assertCount(1, $found);
        $this->assertEquals($target->toArray(), $found->first()->toArray());
    }
}
