<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

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
    public function test_models_can_be_found_using_default_search(): void
    {
        factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();

        $target = factory(Book::class)->state('belongs_to_author')->create(['title' => uniqid('test')]);
        $found = Book::search($target->title)->orderBy('id')->get();

        $this->assertCount(1, $found);
        $this->assertEquals($target->toArray(), $found->first()->toArray());
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

        $source = factory(Book::class, rand(2, 10))->state('belongs_to_author')->create();
        $found = Book::search()->get();

        // assert that the amount of created models corresponds number of found models
        $this->assertSame($source->count(), $found->count());
        // assert that all source models are found
        $this->assertCount(0, $source->pluck('id')->diff($found->pluck('id')));
    }

    /**
     * @dataProvider queueConfigProvider
     */
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
}
