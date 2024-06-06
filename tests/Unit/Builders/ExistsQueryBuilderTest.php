<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\ExistsQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(ExistsQueryBuilder::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(FlatArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
final class ExistsQueryBuilderTest extends TestCase
{
    private ExistsQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ExistsQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->buildQuery();
    }

    public function test_query_with_field_can_be_built(): void
    {
        $expected = [
            'exists' => [
                'field' => 'message',
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
