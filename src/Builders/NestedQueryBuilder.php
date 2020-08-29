<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\SharedParameters\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\QueryParameter;
use ElasticScoutDriverPlus\Builders\SharedParameters\ScoreModeParameter;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class NestedQueryBuilder implements QueryBuilderInterface
{
    use QueryParameter;
    use ScoreModeParameter;
    use IgnoreUnmappedParameter;

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
        if (is_null($this->path)) {
            throw new QueryBuilderException('Path is not specified');
        }

        if (is_null($this->query)) {
            throw new QueryBuilderException('Query is not specified');
        }

        $nested = [
            'path' => $this->path,
            'query' => $this->query,
        ];

        if (isset($this->scoreMode)) {
            $nested['score_mode'] = $this->scoreMode;
        }

        if (isset($this->ignoreUnmapped)) {
            $nested['ignore_unmapped'] = $this->ignoreUnmapped;
        }

        return compact('nested');
    }
}
