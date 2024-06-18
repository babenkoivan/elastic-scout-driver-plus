<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Carbon\Carbon;
use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchAllQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\RangeQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Builders\TermQueryBuilder;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\QueryStringParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\CompoundValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Arr;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

use PHPUnit\Framework\Attributes\UsesClass;

use const SORT_NUMERIC;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(BoolQueryBuilder::class)]
#[CoversClass(Engine::class)]
#[CoversClass(LazyModelFactory::class)]
#[CoversClass(ModelFactory::class)]
#[CoversClass(Query::class)]
#[UsesClass(DatabaseQueryBuilder::class)]
#[UsesClass(MatchAllQueryBuilder::class)]
#[UsesClass(MatchQueryBuilder::class)]
#[UsesClass(RangeQueryBuilder::class)]
#[UsesClass(SearchParametersBuilder::class)]
#[UsesClass(TermQueryBuilder::class)]
#[UsesClass(Hit::class)]
#[UsesClass(SearchResult::class)]
#[UsesClass(DocumentFactory::class)]
#[UsesClass(ParameterFactory::class)]
#[UsesClass(RoutingFactory::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(FieldParameter::class)]
#[UsesClass(QueryStringParameter::class)]
#[UsesClass(ValueParameter::class)]
#[UsesClass(FlatArrayTransformer::class)]
#[UsesClass(GroupedArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
#[UsesClass(CompoundValidator::class)]
#[UsesClass(OneOfValidator::class)]
#[UsesClass(Searchable::class)]
#[UsesClass(Arr::class)]
final class BoolQueryTest extends TestCase
{
    public function test_models_can_be_found_using_must(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $query = Query::bool()->must(
            Query::match()
                ->field('title')
                ->query($target->title)
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_must_not(): void
    {
        $mixin = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('test')]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $query = Query::bool()->mustNot(
            Query::match()
                ->field('title')
                ->query($mixin->title)
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_using_should(): void
    {
        $source = collect(['2018-04-23', '2003-01-14', '2020-03-07'])->map(
            static fn (string $published) => factory(Book::class)
                ->state('belongs_to_author')
                ->create(['published' => Carbon::createFromFormat('Y-m-d', $published)])
        );

        $target = $source->filter(
            static fn (Book $model) => $model->published->year > 2003
        )->sortBy('id', SORT_NUMERIC);

        $query = Query::bool()
            ->should(
                Query::term()
                    ->field('published')
                    ->value('2018-04-23')
            )
            ->should(
                Query::term()
                    ->field('published')
                    ->value('2020-03-07')
            );

        $found = Book::searchQuery($query)
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }

    public function test_models_can_be_found_using_filter(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2010, 5, 10)]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => Carbon::create(2020, 6, 7)]);

        $query = Query::bool()->filter(
            Query::term()
                ->field('published')
                ->value('2020-06-07')
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_not_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $source = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = $source->first();

        $source->where('id', '!=', $target->id)->each(static function (Book $model) {
            $model->delete();
        });

        $query = Query::bool()->must(
            Query::matchAll()
        );

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $target = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        // soft delete some models
        $target->first()->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->withTrashed();

        $found = Book::searchQuery($query)
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }

    public function test_only_trashed_models_can_be_found(): void
    {
        $this->config->set('scout.soft_delete', true);

        $source = factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $target = $source->first();
        $target->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->onlyTrashed();

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_only_trashed_models_can_be_found_in_multiple_indices(): void
    {
        $this->config->set('scout.soft_delete', true);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create();

        $target->delete();

        $query = Query::bool()
            ->must(Query::matchAll())
            ->onlyTrashed();

        $found = Author::searchQuery($query)
            ->join(Book::class)
            ->execute();

        $this->assertFoundModel($target, $found);
    }

    public function test_models_can_be_found_in_multiple_indices(): void
    {
        // additional mixins
        factory(Book::class, rand(2, 10))
            ->state('belongs_to_author')
            ->create();

        $firstTarget = factory(Author::class)
            ->state('has_books')
            ->create(['name' => uniqid('author', true)]);

        $secondTarget = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => uniqid('book', true)]);

        $query = Query::bool()
            ->should(
                Query::match()
                    ->field('name')
                    ->query($firstTarget->name)
            )
            ->should(
                Query::match()
                    ->field('title')
                    ->query($secondTarget->title)
            )
            ->minimumShouldMatch(1);

        $found = Author::searchQuery($query)
            ->join(Book::class)
            ->sort('_index')
            ->execute();

        $this->assertFoundModels(collect([$firstTarget, $secondTarget]), $found);
    }

    public function test_models_can_be_found_using_query_builder(): void
    {
        // additional mixin
        factory(Book::class, rand(2, 5))
            ->state('belongs_to_author')
            ->create(['published' => '2019-03-07']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['published' => '2020-12-07']);

        $builder = (new BoolQueryBuilder())->must(
            (new RangeQueryBuilder())
                ->field('published')
                ->gte('2020')
                ->format('yyyy')
        );

        $found = Book::searchQuery($builder)->execute();

        $this->assertFoundModel($target, $found);
    }
}
