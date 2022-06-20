<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Transformers\ArrayTransformerInterface;
use ElasticScoutDriverPlus\QueryParameters\Validators\ValidatorInterface;

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
