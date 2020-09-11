<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\IgnoreUnmappedParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\QueryArrayParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ScoreModeParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

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
        $this->validator = new AllOfValidator(['path', 'query']);
        $this->transformer = new FlatArrayTransformer();
    }

    public function path(string $path): self
    {
        $this->parameters->put('path', $path);
        return $this;
    }
}
