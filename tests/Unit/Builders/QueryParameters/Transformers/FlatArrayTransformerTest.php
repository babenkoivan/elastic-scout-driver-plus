<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders\QueryParameters\Transformers;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 */
final class FlatArrayTransformerTest extends TestCase
{
    public function test_parameters_can_be_transformed_to_flat_array(): void
    {
        $parameters = new Collection([
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
