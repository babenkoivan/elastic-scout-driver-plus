<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\OneOfValidator;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\OneOfValidator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 */
final class OneOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_all_required_parameters_are_missing(): void
    {
        $this->expectException(QueryBuilderException::class);

        $parameters = new Collection(['minimum_should_match' => 1]);
        $validator = new OneOfValidator(['must', 'should']);

        $validator->validate($parameters);
    }

    public function test_exception_is_not_thrown_when_one_of_required_parameters_is_specified(): void
    {
        $parameters = new Collection(['must' => ['match_all' => new stdClass()]]);
        $validator = new OneOfValidator(['must', 'should']);

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
