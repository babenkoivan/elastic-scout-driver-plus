<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\Collection;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;

final class IdsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    /**
     * @var string
     */
    protected $type = 'ids';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new AllOfValidator(['values']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }

    public function values(array $values): self
    {
        $this->parameters->put('values', $values);
        return $this;
    }
}
