<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

use Closure;
use Elastic\ScoutDriverPlus\Factories\ParameterFactory;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Shared\MinimumShouldMatchParameter;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use Elastic\ScoutDriverPlus\Support\Arr;

final class BoolQueryBuilder extends AbstractParameterizedQueryBuilder
{
    use MinimumShouldMatchParameter;

    protected string $type = 'bool';
    private ?int $softDeleted = 0;

    public function __construct()
    {
        $this->parameters = new ParameterCollection();
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
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function must($query): self
    {
        $this->parameters->push('must', ParameterFactory::makeQuery($query));
        return $this;
    }

    public function mustRaw(array $must): self
    {
        $this->parameters->put('must', $must);
        return $this;
    }

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function mustNot($query): self
    {
        $this->parameters->push('must_not', ParameterFactory::makeQuery($query));
        return $this;
    }

    public function mustNotRaw(array $mustNot): self
    {
        $this->parameters->put('must_not', $mustNot);
        return $this;
    }

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function should($query): self
    {
        $this->parameters->push('should', ParameterFactory::makeQuery($query));
        return $this;
    }

    public function shouldRaw(array $should): self
    {
        $this->parameters->put('should', $should);
        return $this;
    }

    /**
     * @param Closure|QueryBuilderInterface|array $query
     */
    public function filter($query): self
    {
        $this->parameters->push('filter', ParameterFactory::makeQuery($query));
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
            $query[$this->type]['filter'] = isset($query[$this->type]['filter'])
                ? Arr::wrapAssoc($query[$this->type]['filter'])
                : [];

            $query[$this->type]['filter'][] = [
                'term' => [
                    '__soft_deleted' => $this->softDeleted,
                ],
            ];
        }

        return $query;
    }
}
