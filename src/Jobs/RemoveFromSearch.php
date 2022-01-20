<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Jobs;

use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Documents\Routing;
use ElasticScoutDriverPlus\Factories\RoutingFactoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class RemoveFromSearch implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    public $indexName;
    /**
     * @var Routing
     */
    public $routing;
    /**
     * @var string[]
     */
    public $documentIds;

    public function __construct(Collection $models)
    {
        $this->indexName = $models->first()->searchableAs();
        $this->routing = app(RoutingFactoryInterface::class)->makeFromModels($models);

        $this->documentIds = $models->map(static function (Model $model) {
            return (string)$model->getScoutKey();
        })->all();
    }

    public function handle(DocumentManager $documentManager): void
    {
        $refreshDocuments = config('elastic.scout_driver.refresh_documents');
        $documentManager->delete($this->indexName, $this->documentIds, $refreshDocuments, $this->routing);
    }
}
