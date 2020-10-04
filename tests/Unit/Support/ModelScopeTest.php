<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Support;

use ElasticScoutDriverPlus\Support\ModelScope;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/**
 * @covers \ElasticScoutDriverPlus\Support\ModelScope
 */
final class ModelScopeTest extends TestCase
{
    /**
     * @var ModelScope
     */
    private $modelScope;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelScope = new ModelScope(Book::class);
    }

    public function test_exception_is_thrown_when_pushing_not_searchable_model_in_scope(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->modelScope->push(self::class);
    }

    public function test_model_can_be_pushed_in_scope(): void
    {
        $this->modelScope->push(Author::class);

        $this->assertTrue($this->modelScope->has(Author::class));
    }

    public function test_model_existence_in_scope_can_be_checked(): void
    {
        $this->assertTrue($this->modelScope->has(Book::class));
    }

    public function test_default_query_can_be_retrieved_from_scope(): void
    {
        $this->assertInstanceOf(
            Book::class,
            $this->modelScope->getDefaultQuery()->getModel()
        );
    }

    public function test_exception_is_thrown_when_trying_to_retrieve_non_existing_query(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->modelScope->getQuery(Author::class);
    }

    public function test_query_can_be_retrieved_from_scope(): void
    {
        $this->assertInstanceOf(
            Book::class,
            $this->modelScope->getQuery(Book::class)->getModel()
        );
    }

    public function test_scope_queries_can_be_keyed_by_index_name(): void
    {
        $this->modelScope->push(Author::class);

        $keyedQueries = $this->modelScope->keyQueriesByIndexName();

        $this->assertCount(2, $keyedQueries);
        $this->assertSame(['books', 'authors'], $keyedQueries->keys()->toArray());
        $this->assertInstanceOf(Builder::class, $keyedQueries->get('books'));
        $this->assertInstanceOf(Builder::class, $keyedQueries->get('authors'));
        $this->assertInstanceOf(Book::class, $keyedQueries->get('books')->getModel());
        $this->assertInstanceOf(Author::class, $keyedQueries->get('authors')->getModel());
    }

    public function test_index_names_can_be_resolved(): void
    {
        $this->modelScope->push(Author::class);

        $this->assertSame(['books', 'authors'], $this->modelScope->resolveIndexNames()->toArray());
    }
}
