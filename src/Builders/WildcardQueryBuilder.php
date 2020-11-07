<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\FieldParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\RewriteParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\ValueParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;

final class WildcardQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use BoostParameter;
    use RewriteParameter;

    /**
     * @var string
     */
    protected $query = 'wildcard';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
