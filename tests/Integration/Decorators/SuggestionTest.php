<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Decorators;

use Elastic\Adapter\Search\Suggestion as BaseSuggestion;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Decorators\Suggestion;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \Elastic\ScoutDriverPlus\Decorators\Suggestion
 *
 * @uses   \Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder
 * @uses   \Elastic\ScoutDriverPlus\Engine
 * @uses   \Elastic\ScoutDriverPlus\Factories\DocumentFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\ModelFactory
 * @uses   \Elastic\ScoutDriverPlus\Factories\RoutingFactory
 * @uses   \Elastic\ScoutDriverPlus\Searchable
 */
final class SuggestionTest extends TestCase
{
    private Collection $models;
    private Suggestion $suggestion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->models = factory(Author::class, 5)->create();

        $baseSuggestion = new BaseSuggestion([
            'text' => 'tes',
            'offset' => 0,
            'length' => 3,
            'options' => $this->models->map(
                static fn (Model $model) => [
                    'text' => 'test' . $model->getScoutKey(),
                    '_index' => $model->searchableAs(),
                    '_id' => (string)$model->getScoutKey(),
                ]
            ),
        ]);

        $modelFactory = new ModelFactory([
            $this->models->first()->searchableAs() => new DatabaseQueryBuilder($this->models->first()),
        ]);

        $this->suggestion = new Suggestion($baseSuggestion, $modelFactory);
    }

    public function test_models_can_be_retrieved(): void
    {
        $this->assertEquals(
            $this->models->toArray(),
            $this->suggestion->models()->toArray()
        );
    }
}
