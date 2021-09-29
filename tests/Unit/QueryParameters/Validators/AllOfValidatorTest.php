<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 */
final class AllOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_one_of_required_parameters_is_missing(): void
    {
        $this->expectException(QueryBuilderException::class);

        $parameters = new ParameterCollection(['field' => 'title']);
        $validator = new AllOfValidator(['field', 'query']);

        $validator->validate($parameters);
    }

    public function test_exception_is_not_thrown_when_all_required_parameters_are_specified(): void
    {
        $parameters = new ParameterCollection(['field' => 'title', 'query' => 'book']);
        $validator = new AllOfValidator(['field', 'query']);

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
