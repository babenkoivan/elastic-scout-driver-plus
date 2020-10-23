<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\NullValidator;

final class TermsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use BoostParameter;

    /**
     * @var string
     */
    protected $query = 'terms';

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new NullValidator();
        $this->parameterTransformer = new FlatArrayTransformer();
    }

    public function terms(string $field, array $terms): self
    {
        $this->parameters->put($field, $terms);
        return $this;
    }
}
