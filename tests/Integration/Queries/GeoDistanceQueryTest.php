<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Queries;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\GeoDistanceQueryBuilder;
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
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Query;
use Elastic\ScoutDriverPlus\Tests\App\Store;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(GeoDistanceQueryBuilder::class)]
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
#[UsesClass(CallbackArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
#[UsesClass(Searchable::class)]
final class GeoDistanceQueryTest extends TestCase
{
    public function test_models_can_be_found_using_field_and_distance_and_lat_lon(): void
    {
        // additional mixin
        factory(Store::class, rand(2, 10))->create([
            'lat' => 20,
            'lon' => 20,
        ]);

        $target = factory(Store::class)->create([
            'lat' => 10,
            'lon' => 10,
        ]);

        $query = Query::geoDistance()
            ->field('location')
            ->distance('500km')
            ->lat(8)
            ->lon(8);

        $found = Store::searchQuery($query)->execute();

        $this->assertFoundModel($target, $found);
    }
}
