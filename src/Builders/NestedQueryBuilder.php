<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\QueryParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ScoreModeParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use stdClass;

final class NestedQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use QueryParameter;
    use ScoreModeParameter;
    use IgnoreUnmappedParameter;

    /**
     * @var string
     */
    protected $type = 'nested';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['path', 'query']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }

    public function path(string $path): self
    {
        $this->parameters->put('path', $path);
        return $this;
    }

    public function innerHits(array $options = []): self
    {
        $this->parameters->put('inner_hits', empty($options) ? new stdClass() : $options);
        return $this;
    }
}
