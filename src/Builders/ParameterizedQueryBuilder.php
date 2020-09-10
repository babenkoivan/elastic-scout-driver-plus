<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\ArrayTransformerInterface;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\ValidatorInterface;

abstract class ParameterizedQueryBuilder implements QueryBuilderInterface
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
    protected $validator;
    /**
     * @var ArrayTransformerInterface
     */
    protected $transformer;

    public function buildQuery(): array
    {
        $this->validator->validate($this->parameters);

        return [
            $this->type => $this->transformer->transform($this->parameters),
        ];
    }
}
