<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\GeoShapeQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Builders\GeoShapeQueryBuilder
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class GeoShapeQueryBuilderTest extends TestCase
{
    private GeoShapeQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new GeoShapeQueryBuilder();
    }

    public function test_exception_is_thrown_when_required_parameters_are_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);
        $this->builder->buildQuery();
    }

    public function test_query_with_field_and_shape_and_relation_can_be_built(): void
    {
        $expected = [
            'geo_shape' => [
                'location' => [
                    'shape' => [
                        'type' => 'envelope',
                        'coordinates' => [[13.0, 53.0], [14.0, 52.0]],
                    ],
                    'relation' => 'within',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('location')
            ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
            ->relation('within')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_shape_and_relation_and_ignore_unmapped_can_be_built(): void
    {
        $expected = [
            'geo_shape' => [
                'location' => [
                    'shape' => [
                        'type' => 'envelope',
                        'coordinates' => [[13.0, 53.0], [14.0, 52.0]],
                    ],
                    'relation' => 'within',
                    'ignore_unmapped' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('location')
            ->shape('envelope', [[13.0, 53.0], [14.0, 52.0]])
            ->relation('within')
            ->ignoreUnmapped(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
