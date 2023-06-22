<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\QueryParameters\Transformers;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\ScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class GroupedArrayTransformerTest extends TestCase
{
    public function test_parameters_can_be_transformed_to_grouped_array(): void
    {
        $parameters = new ParameterCollection([
            'field' => 'title',
            'query' => 'The Best Book',
            'operator' => 'AND',
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
