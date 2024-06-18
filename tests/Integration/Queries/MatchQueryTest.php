<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Decorators\SearchResult;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(MatchQueryBuilder::class)]
#[CoversClass(Engine::class)]
#[CoversClass(LazyModelFactory::class)]
#[CoversClass(ModelFactory::class)]
#[CoversClass(Query::class)]
#[UsesClass(DatabaseQueryBuilder::class)]
#[UsesClass(SearchParametersBuilder::class)]
#[UsesClass(Hit::class)]
#[UsesClass(SearchResult::class)]
#[UsesClass(DocumentFactory::class)]
#[UsesClass(ParameterFactory::class)]
#[UsesClass(RoutingFactory::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(GroupedArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
#[UsesClass(Searchable::class)]
final class MatchQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_text(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'foo']);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['title' => 'bar']);

        $query = Query::match()
            ->field('title')
            ->query('bar');

        $found = Book::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }
}
