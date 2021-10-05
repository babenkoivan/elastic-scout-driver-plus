<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

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

    public function transform(ParameterCollection $parameters): array
    {
        return [
            $parameters->get($this->groupKey) => $parameters->except($this->groupKey)->excludeEmpty()->toArray(),
        ];
    }
}
