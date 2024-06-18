<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\MatchAllQueryBuilder;
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
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use PHPUnit\Framework\Attributes\UsesClass;

use const SORT_NUMERIC;

#[CoversClass(MatchAllQueryBuilder::class)]
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
#[UsesClass(Searchable::class)]
final class MatchAllQueryTest extends TestCase
{
    public function test_all_models_can_be_found(): void
    {
        $target = factory(Book::class, rand(8, 10))
            ->state('belongs_to_author')
            ->create()
            ->sortBy('id', SORT_NUMERIC);

        $found = Book::searchQuery(Query::matchAll())
            ->sort('id')
            ->execute();

        $this->assertFoundModels($target, $found);
    }
}
