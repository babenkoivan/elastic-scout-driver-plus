<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\QueryParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ScoreModeParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\Support\ObjectVariables;

final class NestedQueryBuilder implements QueryBuilderInterface
{
    use QueryParameter;
    use ScoreModeParameter;
    use IgnoreUnmappedParameter;
    use ObjectVariables;

    /**
     * @var string|null
     */
    private $path;

    public function path(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function buildQuery(): array
    {
        if (!isset($this->path, $this->query)) {
            throw new QueryBuilderException('Path and query have to be specified');
        }

        return [
            'nested' => $this->getObjectVariables()
                ->whereNotNull()
                ->toArray(),
        ];
    }
}
