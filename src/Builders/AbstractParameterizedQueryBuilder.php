<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Transformers\ArrayTransformerInterface;
use ElasticScoutDriverPlus\QueryParameters\Validators\ValidatorInterface;

abstract class AbstractParameterizedQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var ParameterCollection
     */
    protected $parameters;
    /**
     * @var ValidatorInterface
     */
    protected $parameterValidator;
    /**
     * @var ArrayTransformerInterface
     */
    protected $parameterTransformer;

    public function buildQuery(): array
    {
        $this->parameterValidator->validate($this->parameters);

        return [
            $this->type => $this->parameterTransformer->transform($this->parameters),
        ];
    }
}
