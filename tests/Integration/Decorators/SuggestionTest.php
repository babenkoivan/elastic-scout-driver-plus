<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Decorators;

use Elastic\Adapter\Search\Suggestion as BaseSuggestion;
use Elastic\ScoutDriverPlus\Builders\DatabaseQueryBuilder;
use Elastic\ScoutDriverPlus\Decorators\Suggestion;
use Elastic\ScoutDriverPlus\Engine;
use Elastic\ScoutDriverPlus\Factories\DocumentFactory;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Elastic\ScoutDriverPlus\Factories\RoutingFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Tests\App\Author;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Suggestion::class)]
#[UsesClass(DatabaseQueryBuilder::class)]
#[UsesClass(Engine::class)]
#[UsesClass(DocumentFactory::class)]
#[UsesClass(ModelFactory::class)]
#[UsesClass(RoutingFactory::class)]
#[UsesClass(Searchable::class)]
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
