<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use ElasticScoutDriver\Factories\DocumentFactory as BaseDocumentFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;

class DocumentFactory extends BaseDocumentFactory
{
    public function makeFromModels(BaseCollection $models): BaseCollection
    {
        $models = new EloquentCollection($models);

        if ($searchableWith = $models->first()->searchableWith()) {
            $models->loadMissing($searchableWith);
        }

        return parent::makeFromModels($models);
    }
}
