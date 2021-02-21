<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters;

use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\QueryParameters\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Factory
 *
 * @uses   \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @uses   \ElasticScoutDriverPlus\Builders\MatchQueryBuilder
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Shared\QueryStringParameter
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class FactoryTest extends TestCase
{
    public function test_query_can_be_made_out_of_type_only(): void
    {
        $expectedQuery = [
            'match_all' => new \stdClass(),
        ];

        $actualQuery = Factory::makeQuery('match_all');

        $this->assertEquals($expectedQuery, $actualQuery);
    }

    public function test_query_can_be_made_out_of_type_and_query_body(): void
    {
        $expectedQuery = [
            'match' => [
                'title' => 'The Book',
            ],
        ];

        $actualQuery = Factory::makeQuery('match', ['title' => 'The Book']);

        $this->assertSame($expectedQuery, $actualQuery);
    }

    public function test_query_can_be_made_out_of_array(): void
    {
        $expectedQuery = [
            'match' => [
                'title' => 'The Book',
            ],
        ];

        $actualQuery = Factory::makeQuery([
            'match' => [
                'title' => 'The Book',
            ],
        ]);

        $this->assertSame($expectedQuery, $actualQuery);
    }

    public function test_query_can_be_made_out_of_query_builder(): void
    {
        $expectedQuery = [
            'match' => [
                'title' => [
                    'query' => 'The Book',
                ],
            ],
        ];

        $actualQuery = Factory::makeQuery(
            (new MatchQueryBuilder())
                ->field('title')
                ->query('The Book')
        );

        $this->assertSame($expectedQuery, $actualQuery);
    }
}
