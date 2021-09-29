<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValuesParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class IdsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use ValuesParameter;

    /**
     * @var string
     */
    protected $type = 'ids';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['values']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
