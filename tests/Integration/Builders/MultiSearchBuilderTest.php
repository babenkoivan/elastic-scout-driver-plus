<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Builders;

use Elastic\Adapter\Exceptions\SearchOperationException;
use Elastic\ScoutDriverPlus\Builders\MultiSearchBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Exceptions\ConnectionMismatchException;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\App\Model;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;

#[CoversClass(MultiSearchBuilder::class)]
#[CoversClass(ConnectionMismatchException::class)]
#[UsesClass(SearchParametersBuilder::class)]
#[UsesClass(Engine::class)]
#[UsesClass(ModelFactory::class)]
final class MultiSearchBuilderTest extends TestCase
{
    public function test_results_can_be_retrieved_for_multiple_models_with_named_keys(): void
    {
        $book = factory(Book::class)->state('belongs_to_author')->create();
        $author = $book->author;

        $results = (new MultiSearchBuilder())
            ->add(Book::searchQuery(['match_all' => new stdClass()])->load(['author']), 'books')
            ->add(Author::searchQuery(['match_all' => new stdClass()]), 'authors')
            ->execute();

        $this->assertFoundModel($book, $results->get('books'));
        $this->assertFoundModel($author, $results->get('authors'));

        $results->get('books')->models()->each(function (Model $model) {
            $this->assertTrue($model->relationLoaded('author'));
        });
    }

    public function test_results_preserve_order_for_unnamed_keys(): void
    {
        $book = factory(Book::class)->state('belongs_to_author')->create();
        $author = $book->author;

        $results = (new MultiSearchBuilder())
            ->add(Book::searchQuery(['match_all' => new stdClass()]))
            ->add(Author::searchQuery(['match_all' => new stdClass()]))
            ->execute();

        $this->assertSame([0, 1], $results->keys()->all());
        $this->assertFoundModel($book, $results->get(0));
        $this->assertFoundModel($author, $results->get(1));
    }

    public function test_raw_results_can_be_retrieved(): void
    {
        factory(Book::class)->state('belongs_to_author')->create();
        factory(Author::class)->create();

        $raw = (new MultiSearchBuilder())
            ->add(Book::searchQuery(['match_all' => new stdClass()]), 'books')
            ->add(Author::searchQuery(['match_all' => new stdClass()]), 'authors')
            ->raw();

        $this->assertIsArray($raw->get('books'));
        $this->assertIsArray($raw->get('authors'));
        $this->assertArrayHasKey('hits', $raw->get('books'));
        $this->assertArrayHasKey('hits', $raw->get('authors'));
    }

    public function test_exception_is_thrown_for_mixed_connections(): void
    {
        $this->config->set(
            'elastic.client.connections.secondary',
            $this->config->get('elastic.client.connections.default')
        );

        $defaultBuilder = Book::searchQuery(['match_all' => new stdClass()]);
        $secondaryBuilder = Book::searchQuery(['match_all' => new stdClass()]);

        // the builder resolves its engine from the model at construction time and exposes no public
        // way to override it afterwards, so a bound closure is used to simulate a model whose
        // searchableConnection() returns a different connection than the default one.
        $secondaryEngine = (new Book())->searchableUsing()->connection('secondary');
        (function () use ($secondaryEngine) {
            $this->engine = $secondaryEngine;
        })->call($secondaryBuilder);

        $this->expectException(ConnectionMismatchException::class);

        (new MultiSearchBuilder())
            ->add($defaultBuilder)
            ->add($secondaryBuilder)
            ->execute();
    }

    public function test_exception_is_thrown_when_default_connection_is_not_named_default(): void
    {
        $this->config->set(
            'elastic.client.connections.eu',
            $this->config->get('elastic.client.connections.default')
        );
        $this->config->set('elastic.client.default', 'eu');

        $implicitBuilder = Book::searchQuery(['match_all' => new stdClass()]);
        $explicitBuilder = Book::searchQuery(['match_all' => new stdClass()]);

        // simulate a model whose searchableConnection() explicitly returns "default",
        // while the actual default connection is "eu"
        $explicitEngine = (new Book())->searchableUsing()->connection('default');
        (function () use ($explicitEngine) {
            $this->engine = $explicitEngine;
        })->call($explicitBuilder);

        $this->expectException(ConnectionMismatchException::class);

        (new MultiSearchBuilder())
            ->add($implicitBuilder)
            ->add($explicitBuilder)
            ->execute();
    }

    public function test_empty_collection_is_returned_when_no_search_was_added(): void
    {
        $builder = new MultiSearchBuilder();

        $this->assertTrue($builder->execute()->isEmpty());
        $this->assertTrue($builder->raw()->isEmpty());
    }

    public function test_exception_is_thrown_when_one_of_the_searches_fails(): void
    {
        factory(Book::class)->state('belongs_to_author')->create();

        $this->expectException(SearchOperationException::class);

        (new MultiSearchBuilder())
            ->add(Book::searchQuery(['match_all' => new stdClass()]))
            ->add(Book::searchQuery(['match_all' => new stdClass()])->sort('unknown_field', 'asc'))
            ->execute();
    }

    public function test_relations_are_loaded_the_same_way_as_a_single_search(): void
    {
        factory(Book::class, 5)->state('belongs_to_author')->create();

        $standalone = Book::searchQuery(['match_all' => new stdClass()])
            ->load(['author'])
            ->execute();

        $batched = (new MultiSearchBuilder())
            ->add(Book::searchQuery(['match_all' => new stdClass()])->load(['author']))
            ->execute()
            ->get(0);

        $this->assertEquals($standalone->models()->toArray(), $batched->models()->toArray());

        $batched->models()->each(function (Model $model) {
            $this->assertTrue($model->relationLoaded('author'));
        });
    }
}
