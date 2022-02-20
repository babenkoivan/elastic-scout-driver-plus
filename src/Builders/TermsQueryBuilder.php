<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValuesParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class TermsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValuesParameter;
    use BoostParameter;

    /**
     * @var string
     */
    protected $type = 'terms';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();

        $this->parameterValidator = new AllOfValidator(['field', 'values']);

        $this->parameterTransformer = new CallbackArrayTransformer(static function (ParameterCollection $parameters) {
            return array_merge(
                [$parameters->get('field') => $parameters->get('values')],
                $parameters->except(['field', 'values'])->excludeEmpty()->toArray(),
            );
        });
    }
}
