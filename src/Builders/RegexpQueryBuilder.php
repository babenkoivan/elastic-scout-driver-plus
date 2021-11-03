<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\FlagsParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\MaxDeterminizedStatesParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class RegexpQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use FlagsParameter;
    use MaxDeterminizedStatesParameter;
    use RewriteParameter;

    /**
     * @var string
     */
    protected $type = 'regexp';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
