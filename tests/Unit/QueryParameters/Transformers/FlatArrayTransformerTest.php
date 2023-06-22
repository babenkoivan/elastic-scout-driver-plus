<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\QueryParameters\Transformers;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class FlatArrayTransformerTest extends TestCase
{
    public function test_parameters_can_be_transformed_to_flat_array(): void
    {
        $parameters = new ParameterCollection([
            'fields' => ['title', 'year'],
            'query' => 2020,
            'type' => null,
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
