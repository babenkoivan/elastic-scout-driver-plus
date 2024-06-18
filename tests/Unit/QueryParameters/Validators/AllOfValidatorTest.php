<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AllOfValidator::class)]
#[UsesClass(ParameterCollection::class)]
final class AllOfValidatorTest extends TestCase
{
    public function test_exception_is_thrown_when_one_of_required_parameters_is_missing(): void
    {
        $this->expectException(QueryBuilderValidationException::class);

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
