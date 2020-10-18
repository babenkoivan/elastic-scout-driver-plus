<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FlagsParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MaxDeterminizedStatesParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

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
    protected $query = 'regexp';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
