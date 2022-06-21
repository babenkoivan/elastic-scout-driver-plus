<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\ArrayTransformerInterface;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\ValidatorInterface;

abstract class AbstractParameterizedQueryBuilder implements QueryBuilderInterface
{
    protected string $type;
    protected ParameterCollection $parameters;
    protected ValidatorInterface $parameterValidator;
    protected ArrayTransformerInterface $parameterTransformer;

    public function buildQuery(): array
    {
        $this->parameterValidator->validate($this->parameters);

        return [
            $this->type => $this->parameterTransformer->transform($this->parameters),
        ];
    }
}
