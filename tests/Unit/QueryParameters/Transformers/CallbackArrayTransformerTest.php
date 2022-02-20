<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Transformers\CallbackArrayTransformer
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class CallbackArrayTransformerTest extends TestCase
{
    public function test_callback_can_transform_parameters_to_array(): void
    {
        $parameters = new ParameterCollection([
            'distance' => '200km',
            'field' => 'location',
            'lat' => 40,
            'lon' => 70,
        ]);

        $transformer = new CallbackArrayTransformer(static function (ParameterCollection $parameters) {
            return [
                'distance' => $parameters->get('distance'),
                $parameters->get('field') => [
                    'lat' => $parameters->get('lat'),
                    'lon' => $parameters->get('lon'),
                ],
            ];
        });

        $this->assertSame(
            [
                'distance' => '200km',
                'location' => [
                    'lat' => 40,
                    'lon' => 70,
                ],
            ],
            $transformer->transform($parameters)
        );
    }
}
