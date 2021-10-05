<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class PrefixQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use RewriteParameter;

    /**
     * @var string
     */
    protected $type = 'prefix';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
