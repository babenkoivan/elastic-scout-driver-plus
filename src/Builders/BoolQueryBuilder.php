<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\QueryParameters\Collection;
use ElasticScoutDriverPlus\QueryParameters\Factory;
use ElasticScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use ElasticScoutDriverPlus\Support\Arr;

final class BoolQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use MinimumShouldMatchParameter;

    /**
     * @var string
     */
    protected $type = 'bool';
    /**
     * @var int|null
     */
    private $softDeleted = 0;

    public function __construct()
    {
        $this->parameters = new Collection();
        $this->parameterValidator = new OneOfValidator(['must', 'must_not', 'should', 'filter']);
        $this->parameterTransformer = new FlatArrayTransformer();
    }

    public function withTrashed(): self
    {
        $this->softDeleted = null;
        return $this;
    }

    public function onlyTrashed(): self
    {
        $this->softDeleted = 1;
        return $this;
    }

    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public function must($type, array $query = []): self
    {
        $this->parameters->push('must', Factory::makeQuery($type, $query));
        return $this;
    }

    public function mustRaw(array $must): self
    {
        $this->parameters->put('must', $must);
        return $this;
    }

    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public function mustNot($type, array $query = []): self
    {
        $this->parameters->push('must_not', Factory::makeQuery($type, $query));
        return $this;
    }

    public function mustNotRaw(array $mustNot): self
    {
        $this->parameters->put('must_not', $mustNot);
        return $this;
    }

    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public function should($type, array $query = []): self
    {
        $this->parameters->push('should', Factory::makeQuery($type, $query));
        return $this;
    }

    public function shouldRaw(array $should): self
    {
        $this->parameters->put('should', $should);
        return $this;
    }

    /**
     * @param string|array|QueryBuilderInterface $type
     */
    public function filter($type, array $query = []): self
    {
        $this->parameters->push('filter', Factory::makeQuery($type, $query));
        return $this;
    }

    public function filterRaw(array $filter): self
    {
        $this->parameters->put('filter', $filter);
        return $this;
    }

    public function buildQuery(): array
    {
        $query = parent::buildQuery();

        if (isset($this->softDeleted) && config('scout.soft_delete', false)) {
            $query['bool']['filter'] = isset($query['bool']['filter'])
                ? Arr::wrapAssoc($query['bool']['filter'])
                : [];

            $query['bool']['filter'][] = [
                'term' => [
                    '__soft_deleted' => $this->softDeleted,
                ],
            ];
        }

        return $query;
    }
}
