<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Factories;

use Elastic\ScoutDriver\Factories\DocumentFactory as BaseDocumentFactory;
use Illuminate\Support\Collection as BaseCollection;

class DocumentFactory extends BaseDocumentFactory
{
    public function makeFromModels(BaseCollection $models): BaseCollection
    {
        return parent::makeFromModels($models->withSearchableRelations());
    }
}
