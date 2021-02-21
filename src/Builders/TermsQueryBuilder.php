<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\Collection;
use ElasticScoutDriverPlus\QueryParameters\Shared\BoostParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\NullValidator;

final class TermsQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use BoostParameter;

    /**
     * @var string
     */
    protected $type = 'terms';

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
