<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders\QueryParameters\Transformers;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\GroupedArrayTransformer
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 */
final class GroupedArrayTransformerTest extends TestCase
{
    public function test_parameters_can_be_transformed_to_grouped_array(): void
    {
        $parameters = new Collection([
            'field' => 'title',
            'query' => 'The Best Book',
            'operator' => 'AND',
            'analyzer' => '',
            'lenient' => null,
        ]);

        $transformer = new GroupedArrayTransformer('field');

        $this->assertSame(
            [
                'title' => [
                    'query' => 'The Best Book',
                    'operator' => 'AND',
                ],
            ],
            $transformer->transform($parameters)
        );
    }
}
