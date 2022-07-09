<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Decorators;

use Elastic\Adapter\Search\Suggestion as BaseSuggestion;
use Elastic\ScoutDriverPlus\Factories\ModelFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin BaseSuggestion
 */
final class Suggestion
{
    use ForwardsCalls;

    private BaseSuggestion $baseSuggestion;
    private ModelFactory $modelFactory;
    private Collection $models;

    public function __construct(BaseSuggestion $baseSuggestion, ModelFactory $modelFactory)
    {
        $this->baseSuggestion = $baseSuggestion;
        $this->modelFactory = $modelFactory;
    }

    public function models(): Collection
    {
        if (!isset($this->models)) {
            $this->models = new Collection();

            $groupedDocumentIds = $this->options()->filter(
                static fn (array $option) => isset($option['_index'], $option['_id'])
            )->mapToGroups(
                static fn (array $option) => [(string)$option['_index'] => (string)$option['_id']]
            )->toArray();

            /** @var array $documentIds */
            foreach ($groupedDocumentIds as $indexName => $documentIds) {
                $this->models = $this->models->merge(
                    $this->modelFactory->makeFromIndexNameAndDocumentIds($indexName, $documentIds)
                );
            }
        }

        return $this->models;
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->baseSuggestion, $method, $parameters);
    }
}
