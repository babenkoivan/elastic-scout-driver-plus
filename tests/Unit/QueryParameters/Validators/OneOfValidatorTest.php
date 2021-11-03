<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class OneOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_all_required_parameters_are_missing(): void
    {
        $this->expectException(QueryBuilderException::class);

        $parameters = new ParameterCollection(['minimum_should_match' => 1]);
        $validator = new OneOfValidator(['must', 'should']);

        $validator->validate($parameters);
    }

    public function test_exception_is_not_thrown_when_one_of_required_parameters_is_specified(): void
    {
        $parameters = new ParameterCollection(['must' => ['match_all' => new stdClass()]]);
        $validator = new OneOfValidator(['must', 'should']);

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
