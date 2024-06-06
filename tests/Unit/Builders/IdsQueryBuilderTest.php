<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\IdsQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(IdsQueryBuilder::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(FlatArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
final class IdsQueryBuilderTest extends TestCase
{
    private IdsQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new IdsQueryBuilder();
    }

    public function test_exception_is_thrown_when_values_are_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->buildQuery();
    }

    public function test_query_with_values_can_be_built(): void
    {
        $expected = [
            'ids' => [
                'values' => ['1', '2', '3'],
            ],
        ];

        $actual = $this->builder
            ->values(['1', '2', '3'])
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
