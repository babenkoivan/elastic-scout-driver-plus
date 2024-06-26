<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration;

use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Jobs\RemoveFromSearch;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Engine::class)]
#[CoversClass(RemoveFromSearch::class)]
#[UsesClass(DocumentFactory::class)]
#[UsesClass(RoutingFactory::class)]
#[UsesClass(Searchable::class)]
final class EngineTest extends TestCase
{
    public function test_models_can_be_found_using_default_search(): void
    {
        factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        $target = factory(Book::class)->state('belongs_to_author')->create(['title' => uniqid('test')]);
        $found = Book::search($target->title)->orderBy('id')->get();

        $this->assertCount(1, $found);
        $this->assertEquals($target->toArray(), $found->first()->toArray());
    }

    public static function queueConfigProvider(): array
    {
        return [
            [['scout.queue' => true]],
            [['scout.queue' => false]],
        ];
    }

    #[DataProvider('queueConfigProvider')]
    public function test_models_can_be_indexed(array $config): void
    {
        config($config);

        $source = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();
        $found = Book::search()->get();

        // assert that the amount of created models corresponds number of found models
        $this->assertSame($source->count(), $found->count());
        // assert that all source models are found
        $this->assertCount(0, $source->pluck('id')->diff($found->pluck('id')));
    }

    #[DataProvider('queueConfigProvider')]
    public function test_models_can_be_deleted(array $config): void
    {
        config($config);

        $source = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        // delete newly created models
        $source->each(static function (Book $model) {
            $model->delete();
        });

        // assert that there are no documents in the index
        $found = Book::search()->get();
        $this->assertSame(0, $found->count());
    }

    public function test_point_in_time_can_be_opened_and_closed(): void
    {
        $pit = Book::openPointInTime('1m');
        $this->assertNotNull($pit);

        Book::closePointInTime($pit);
    }

    public function test_relations_can_be_preloaded_when_indexing_all_models(): void
    {
        factory(Book::class, 10)
            ->state('belongs_to_author')
            ->create();

        $this->assertDatabaseQueriesCount(2, static function () {
            Book::makeAllSearchable();
        });
    }
}
