<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters\Transformers;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class FlatArrayTransformerTest extends TestCase
{
    public function test_parameters_can_be_transformed_to_flat_array(): void
    {
        $parameters = new ParameterCollection([
            'fields' => ['title', 'year'],
            'query' => 2020,
            'type' => '',
        ]);

        $transformer = new FlatArrayTransformer();

        $this->assertSame(
            [
                'fields' => ['title', 'year'],
                'query' => 2020,
            ],
            $transformer->transform($parameters)
        );
    }
}
