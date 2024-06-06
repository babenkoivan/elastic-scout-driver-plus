<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\PrefixQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractParameterizedQueryBuilder::class)]
#[CoversClass(PrefixQueryBuilder::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(GroupedArrayTransformer::class)]
#[UsesClass(AllOfValidator::class)]
final class PrefixQueryBuilderTest extends TestCase
{
    private PrefixQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new PrefixQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $this->builder
            ->value('bo')
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
            'prefix' => [
                'title' => [
                    'value' => 'bo',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('bo')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_rewrite_can_be_built(): void
    {
        $expected = [
            'prefix' => [
                'title' => [
                    'value' => 'bo',
                    'rewrite' => 'constant_score',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('bo')
            ->rewrite('constant_score')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_value_and_case_insensitive_can_be_built(): void
    {
        $expected = [
            'prefix' => [
                'title' => [
                    'value' => 'bo',
                    'case_insensitive' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('title')
            ->value('bo')
            ->caseInsensitive(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
