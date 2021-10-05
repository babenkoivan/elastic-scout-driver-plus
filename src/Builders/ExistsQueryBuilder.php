<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class ExistsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;

    /**
     * @var string
     */
    protected $type = 'exists';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
