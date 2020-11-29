<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\ArrayTransformerInterface;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\ValidatorInterface;

abstract class AbstractParameterizedQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var Collection
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
