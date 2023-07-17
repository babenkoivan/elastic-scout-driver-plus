<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Decorators;

use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Decorators\NullEngine
 *
 * @uses \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses \Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder
 * @uses \Elastic\ScoutDriverPlus\Decorators\SearchResult
 * @uses \Elastic\ScoutDriverPlus\Engine
 * @uses \Elastic\ScoutDriverPlus\Factories\LazyModelFactory
 * @uses \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @uses \Elastic\ScoutDriverPlus\Searchable
 */
final class NullEngineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('scout.driver', null);
    }

    public function test_search_with_parameters(): void
    {
        $found = Book::searchQuery()->execute();

        $this->assertCount(0, $found->hits());
        $this->assertSame(0, $found->total());
    }

    public function test_point_in_time(): void
    {
        $pit = Book::openPointInTime();
        $this->assertSame('', $pit);

        Book::closePointInTime($pit);
    }
}
