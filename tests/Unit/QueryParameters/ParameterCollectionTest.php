<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 *
 * @uses   \ElasticScoutDriverPlus\Support\Arr
 */
final class ParameterCollectionTest extends TestCase
{
    /**
     * @var ParameterCollection
     */
    private $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collection = new ParameterCollection([
            'must' => null,
            'should' => ['term' => ['year' => 2020]],
            'filter' => [],
            'minimum_should_match' => 1,
        ]);
    }

    public function test_value_can_be_put_in_the_collection(): void
    {
        $this->collection->put('filter', ['term' => ['author_id' => 1]]);

        $this->assertSame(
            [
                'must' => null,
                'should' => ['term' => ['year' => 2020]],
                'filter' => ['term' => ['author_id' => 1]],
                'minimum_should_match' => 1,
            ],
            $this->collection->toArray()
        );
    }

    public function test_value_can_be_pushed_in_the_collection(): void
    {
        $this->collection->push('must', ['match_all' => new stdClass()]);
        $this->collection->push('should', ['term' => ['year' => 2021]]);

        $this->assertEquals(
            [
                'must' => [
                    ['match_all' => new stdClass()],
                ],
                'should' => [
                    ['term' => ['year' => 2020]],
                    ['term' => ['year' => 2021]],
                ],
                'filter' => [],
                'minimum_should_match' => 1,
            ],
            $this->collection->toArray()
        );
    }

    public function test_value_can_be_retrieved_from_the_collection_by_key(): void
    {
        $this->assertSame(1, $this->collection->get('minimum_should_match'));
    }

    public function test_items_except_with_given_keys_can_be_retrieved_from_the_collection(): void
    {
        $this->assertSame(
            [
                'must' => null,
                'filter' => [],
                'minimum_should_match' => 1,
            ],
            $this->collection->except('should')->toArray()
        );
    }

    public function test_items_with_only_given_keys_can_be_retrieved_from_the_collection(): void
    {
        $this->assertSame(
            [
                'should' => ['term' => ['year' => 2020]],
                'minimum_should_match' => 1,
            ],
            $this->collection->only(['should', 'minimum_should_match'])->toArray()
        );
    }

    public function test_items_with_not_empty_values_can_be_retrieved_from_the_collection(): void
    {
        $this->assertSame(
            [
                'should' => ['term' => ['year' => 2020]],
                'minimum_should_match' => 1,
            ],
            $this->collection->excludeEmpty()->toArray()
        );
    }

    public function test_item_count_can_be_calculated(): void
    {
        $this->assertSame(4, $this->collection->count());
    }

    public function test_collection_can_be_transformed_to_array(): void
    {
        $this->assertSame(
            [
                'must' => null,
                'should' => ['term' => ['year' => 2020]],
                'filter' => [],
                'minimum_should_match' => 1,
            ],
            $this->collection->toArray()
        );
    }
}
