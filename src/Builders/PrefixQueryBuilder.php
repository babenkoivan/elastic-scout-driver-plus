<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\CaseInsensitiveParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\FieldParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\RewriteParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\ValueParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class PrefixQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use FieldParameter;
    use ValueParameter;
    use RewriteParameter;
    use CaseInsensitiveParameter;

    protected string $type = 'prefix';

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
        $this->parameterValidator = new AllOfValidator(['field', 'value']);
        $this->parameterTransformer = new GroupedArrayTransformer('field');
    }
}
