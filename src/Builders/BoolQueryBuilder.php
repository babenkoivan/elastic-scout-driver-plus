<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use Illuminate\Support\Arr;
use stdClass;

final class BoolQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var int|null
     */
    private $softDeleted = 0;
    /**
     * @var array
     */
    private $must = [];
    /**
     * @var array
     */
    private $mustNot = [];
    /**
     * @var array
     */
    private $should = [];
    /**
     * @var int|null
     */
    private $minimumShouldMatch;
    /**
     * @var array
     */
    private $filter = [];

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
        return $this->addClause($this->must, $type, $query);
    }

    public function mustRaw(array $must): self
    {
        $this->must = $must;
        return $this;
    }

    public function mustNot(string $type, array $query = []): self
    {
        return $this->addClause($this->mustNot, $type, $query);
    }

    public function mustNotRaw(array $mustNot): self
    {
        $this->mustNot = $mustNot;
        return $this;
    }

    public function should(string $type, array $query = []): self
    {
        return $this->addClause($this->should, $type, $query);
    }

    public function shouldRaw(array $should): self
    {
        $this->should = $should;
        return $this;
    }

    public function minimumShouldMatch(int $minimumShouldMatch): self
    {
        $this->minimumShouldMatch = $minimumShouldMatch;
        return $this;
    }

    public function filter(string $type, array $query = []): self
    {
        return $this->addClause($this->filter, $type, $query);
    }

    public function filterRaw(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function buildQuery(): array
    {
        $bool = [];

        if (count($this->must) > 0) {
            $bool['must'] = $this->must;
        }

        if (count($this->mustNot) > 0) {
            $bool['must_not'] = $this->mustNot;
        }

        if (count($this->should) > 0) {
            $bool['should'] = $this->should;
        }

        if (count($this->filter) > 0) {
            $bool['filter'] = $this->filter;
        }

        if (isset($this->softDeleted) && config('scout.soft_delete', false)) {
            if (!isset($bool['filter'])) {
                $bool['filter'] = [];
            }

            $this->addClause($bool['filter'], 'term', [
                '__soft_deleted' => $this->softDeleted,
            ]);
        }

        if (count($bool) === 0) {
            throw new QueryBuilderException(
                'At least one of the clauses has to be specified: must, must_not, should or filter'
            );
        }

        if (isset($this->minimumShouldMatch)) {
            $bool['minimum_should_match'] = $this->minimumShouldMatch;
        }

        return compact('bool');
    }

    private function addClause(array &$context, string $type, array $query = []): self
    {
        if (Arr::isAssoc($context)) {
            $context = array_map(static function ($query, $type) {
                return [$type => $query];
            }, $context, array_keys($context));
        }

        $context[] = [
            $type => count($query) > 0 ? $query : new stdClass(),
        ];

        return $this;
    }
}
