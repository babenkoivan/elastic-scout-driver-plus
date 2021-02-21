<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\Collection;
use ElasticScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\QueryArrayParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ScoreModeParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class NestedQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use QueryArrayParameter;
    use ScoreModeParameter;
    use IgnoreUnmappedParameter;

    /**
     * @var string
     */
    protected $type = 'nested';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['path', 'query']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }

    public function path(string $path): self
    {
        $this->parameters->put('path', $path);
        return $this;
    }
}
