<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Transformers;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

final class GroupedArrayTransformer implements ArrayTransformerInterface
{
    /**
     * @var string
     */
    private $groupKey;

    public function __construct(string $groupKey)
    {
        $this->groupKey = $groupKey;
    }

    public function transform(Collection $parameters): array
    {
        return [
            $parameters->get($this->groupKey) => $parameters->except($this->groupKey)->excludeEmpty()->toArray(),
        ];
    }
}
