<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Support;

use ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException;
use ElasticScoutDriverPlus\Support\ModelScope;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use InvalidArgumentException;

/**
 * @covers \ElasticScoutDriverPlus\Support\ModelScope
 *
 * @uses   \ElasticScoutDriverPlus\Exceptions\ModelClassNotFoundInScopeException
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

        $this->assertTrue($this->modelScope->contains(Author::class));
    }

    public function test_model_existence_in_scope_can_be_checked(): void
    {
        $this->assertTrue($this->modelScope->contains(Book::class));
    }

    public function test_exception_is_thrown_when_adding_relations_for_out_of_scope_model(): void
    {
        $this->expectException(ModelClassNotFoundInScopeException::class);

        $this->modelScope->with(['books'], Author::class);
    }

    public function test_base_model_relations_can_be_added_in_scope(): void
    {
        $this->modelScope->with(['author']);

        $this->assertSame(['author'], $this->modelScope->resolveRelations(Book::class));
    }

    public function test_explicit_model_relations_can_be_added_in_scope(): void
    {
        $this->modelScope
            ->push(Author::class)
            ->with(['books'], Author::class);

        $this->assertSame(['books'], $this->modelScope->resolveRelations(Author::class));
    }

    public function test_index_names_can_be_resolved(): void
    {
        $this->modelScope->push(Author::class);

        $this->assertSame(['books', 'authors'], $this->modelScope->resolveIndexNames()->toArray());
    }

    public function test_index_name_can_be_resolved(): void
    {
        $this->assertSame('books', $this->modelScope->resolveIndexName(Book::class));
    }

    public function test_model_class_can_be_resolved(): void
    {
        $this->assertSame(Book::class, $this->modelScope->resolveModelClass('books'));
    }

    public function test_relations_can_be_resolved(): void
    {
        $this->modelScope->with(['author']);

        $this->assertSame(['author'], $this->modelScope->resolveRelations(Book::class));
    }

    public function test_exception_is_thrown_when_setting_query_callback_for_out_of_scope_model(): void
    {
        $queryCallback = static function (EloquentBuilder $query) {
            $query->select('id', 'name', 'last_name');
        };

        $this->expectException(ModelClassNotFoundInScopeException::class);

        $this->modelScope->modifyQuery($queryCallback, Author::class);
    }

    public function test_query_callback_can_be_resolved(): void
    {
        $queryCallback = static function (EloquentBuilder $query) {
            $query->select('id', 'title', 'description');
        };

        $this->modelScope->modifyQuery($queryCallback);

        $this->assertSame($queryCallback, $this->modelScope->resolveQueryCallback(Book::class));
    }

    public function test_explicit_model_query_callback_can_be_added_in_scope(): void
    {
        $queryCallback = static function (EloquentBuilder $query) {
            $query->select('id', 'name', 'last_name');
        };

        $this->modelScope
            ->push(Author::class)
            ->modifyQuery($queryCallback, Author::class);

        $this->assertSame($queryCallback, $this->modelScope->resolveQueryCallback(Author::class));
    }
}
