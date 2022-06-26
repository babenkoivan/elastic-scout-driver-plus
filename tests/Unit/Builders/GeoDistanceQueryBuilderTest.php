<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\Builders;

use Elastic\ScoutDriverPlus\Builders\GeoDistanceQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \Elastic\ScoutDriverPlus\Builders\GeoDistanceQueryBuilder
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class GeoDistanceQueryBuilderTest extends TestCase
{
    private GeoDistanceQueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new GeoDistanceQueryBuilder();
    }

    public function test_exception_is_thrown_when_required_parameters_are_not_specified(): void
    {
        $this->expectException(QueryBuilderValidationException::class);
        $this->builder->buildQuery();
    }

    public function test_query_with_field_and_distance_and_lat_lon_can_be_built(): void
    {
        $expected = [
            'geo_distance' => [
                'pin.location' => [
                    'lat' => 40.0,
                    'lon' => -70.0,
                ],
                'distance' => '200km',
            ],
        ];

        $actual = $this->builder
            ->field('pin.location')
            ->distance('200km')
            ->lat(40)
            ->lon(-70)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_distance_and_lat_lon_and_distance_type_can_be_built(): void
    {
        $expected = [
            'geo_distance' => [
                'pin.location' => [
                    'lat' => 40.0,
                    'lon' => -70.0,
                ],
                'distance' => '200km',
                'distance_type' => 'arc',
            ],
        ];

        $actual = $this->builder
            ->field('pin.location')
            ->distance('200km')
            ->distanceType('arc')
            ->lat(40)
            ->lon(-70)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_distance_and_lat_lon_and_validation_method_can_be_built(): void
    {
        $expected = [
            'geo_distance' => [
                'pin.location' => [
                    'lat' => 40.0,
                    'lon' => -70.0,
                ],
                'distance' => '200km',
                'validation_method' => 'IGNORE_MALFORMED',
            ],
        ];

        $actual = $this->builder
            ->field('pin.location')
            ->distance('200km')
            ->validationMethod('IGNORE_MALFORMED')
            ->lat(40)
            ->lon(-70)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_distance_and_lat_lon_and_ignore_unmapped_can_be_built(): void
    {
        $expected = [
            'geo_distance' => [
                'pin.location' => [
                    'lat' => 40.0,
                    'lon' => -70.0,
                ],
                'distance' => '200km',
                'ignore_unmapped' => true,
            ],
        ];

        $actual = $this->builder
            ->field('pin.location')
            ->distance('200km')
            ->ignoreUnmapped(true)
            ->lat(40)
            ->lon(-70)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
