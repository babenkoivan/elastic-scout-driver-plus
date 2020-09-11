<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Shared\MinimumShouldMatchParameter;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\OneOfValidator;
use ElasticScoutDriverPlus\Support\Arr;
use stdClass;

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
        $this->validator = new OneOfValidator(['must', 'must_not', 'should', 'filter']);
        $this->transformer = new FlatArrayTransformer();
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

    public function must(string $type, array $query = []): self
    {
        $this->parameters->push('must', [
            $type => count($query) > 0 ? $query : new stdClass(),
        ]);

        return $this;
    }

    public function mustRaw(array $must): self
    {
        $this->parameters->put('must', $must);
        return $this;
    }

    public function mustNot(string $type, array $query = []): self
    {
        $this->parameters->push('must_not', [
            $type => count($query) > 0 ? $query : new stdClass(),
        ]);

        return $this;
    }

    public function mustNotRaw(array $mustNot): self
    {
        $this->parameters->put('must_not', $mustNot);
        return $this;
    }

    public function should(string $type, array $query = []): self
    {
        $this->parameters->push('should', [
            $type => count($query) > 0 ? $query : new stdClass(),
        ]);

        return $this;
    }

    public function shouldRaw(array $should): self
    {
        $this->parameters->put('should', $should);
        return $this;
    }

    public function filter(string $type, array $query): self
    {
        $this->parameters->push('filter', [$type => $query]);
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
