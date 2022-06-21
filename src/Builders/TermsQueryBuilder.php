<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValuesParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class TermsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValuesParameter;
    use BoostParameter;

    protected string $type = 'terms';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new AllOfValidator(['field', 'values']);

        $this->parameterTransformer = new CallbackArrayTransformer(
            static fn (ParameterCollection $parameters) => array_merge(
                [$parameters->get('field') => $parameters->get('values')],
                $parameters->except(['field', 'values'])->excludeEmpty()->toArray(),
            )
        );
    }
}
