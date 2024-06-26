<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\WildcardQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(WildcardQueryBuilder::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(GroupedArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
final class WildcardQueryBuilderTest extends TestCase
{
    private WildcardQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new WildcardQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->value('b*k')
            ->buildQuery();
    }

    public function test_exception_is_thrown_when_value_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->field('title')
            ->buildQuery();
    }

    public function test_query_with_field_and_value_can_be_built(): void
    {
        $expected = [
            'wildcard' => [
                'title' => [
                    'value' => 'b*k',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('b*k')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_boost_can_be_built(): void
    {
        $expected = [
            'wildcard' => [
                'title' => [
                    'value' => 'b*k',
                    'boost' => 1.0,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('b*k')
            ->boost(1.0)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_rewrite_can_be_built(): void
    {
        $expected = [
            'wildcard' => [
                'title' => [
                    'value' => 'b*k',
                    'rewrite' => 'constant_score',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('b*k')
            ->rewrite('constant_score')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_case_insensitive_can_be_built(): void
    {
        $expected = [
            'wildcard' => [
                'title' => [
                    'value' => 'b*k',
                    'case_insensitive' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('b*k')
            ->caseInsensitive(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
