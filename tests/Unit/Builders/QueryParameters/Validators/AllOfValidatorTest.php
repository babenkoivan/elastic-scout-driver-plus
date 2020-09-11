<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 */
final class AllOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_one_of_required_parameters_is_missing(): void
    {
        $this->expectException(QueryBuilderException::class);

        $parameters = new Collection(['field' => 'title']);
        $validator = new AllOfValidator(['field', 'query']);

        $validator->validate($parameters);
    }

    public function test_exception_is_not_thrown_when_all_required_parameters_are_specified(): void
    {
        $parameters = new Collection(['field' => 'title', 'query' => 'book']);
        $validator = new AllOfValidator(['field', 'query']);

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
