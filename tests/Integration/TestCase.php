<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use Elastic\Client\ServiceProvider as ElasticClientServiceProvider;
use Elastic\Migrations\ServiceProvider as ElasticMigrationsServiceProvider;
use Elastic\ScoutDriver\ServiceProvider as ElasticScoutDriverServiceProvider;
use ElasticScoutDriverPlus\Decorators\SearchResult;
use ElasticScoutDriverPlus\ServiceProvider as ElasticScoutDriverPlusServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Scout\ScoutServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected Repository $config;

    protected function getPackageProviders($app)
    {
        return [
            ScoutServiceProvider::class,
            ElasticClientServiceProvider::class,
            ElasticMigrationsServiceProvider::class,
            ElasticScoutDriverServiceProvider::class,
            ElasticScoutDriverPlusServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->config = $app['config'];
        $this->config->set('scout.driver', 'elastic');
        $this->config->set('elastic.migrations.storage_directory', dirname(__DIR__) . '/App/elastic/migrations');
        $this->config->set('elastic.scout_driver.refresh_documents', true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__) . '/App/database/migrations');
        $this->withFactories(dirname(__DIR__) . '/App/database/factories');

        $this->artisan('migrate')->run();
        $this->artisan('elastic:migrate')->run();
    }

    protected function tearDown(): void
    {
        $this->artisan('elastic:migrate:reset')->run();
        $this->artisan('migrate:reset')->run();

        parent::tearDown();
    }

    protected function assertFoundModel(Model $model, SearchResult $searchResult): void
    {
        $this->assertCount(1, $searchResult->models());
        $this->assertEquals($model->toArray(), $searchResult->models()->first()->toArray());
    }

    protected function assertFoundModels(Collection $models, SearchResult $searchResult): void
    {
        $this->assertEquals($models->values()->toArray(), $searchResult->models()->values()->toArray());
    }
}
