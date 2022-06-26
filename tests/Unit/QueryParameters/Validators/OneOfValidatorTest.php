<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 *
 * @uses   \Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class OneOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_all_required_parameters_are_missing(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

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
