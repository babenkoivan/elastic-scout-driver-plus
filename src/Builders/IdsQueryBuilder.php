<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValuesParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class IdsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use ValuesParameter;

    protected string $type = 'ids';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['values']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }
}
