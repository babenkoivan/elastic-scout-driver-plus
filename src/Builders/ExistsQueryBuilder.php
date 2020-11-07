<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

final class ExistsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;

    /**
     * @var string
     */
    protected $query = 'exists';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
