<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\CompoundValidator;
use Elastic\ScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(CompoundValidator::class)]
#[UsesClass(ParameterCollection::class)]
#[UsesClass(AllOfValidator::class)]
#[UsesClass(OneOfValidator::class)]
final class CompoundValidatorTest extends TestCase
{
    #[TestWith([['field' => 'age']])]
    #[TestWith([['gt' => 10]])]
    #[TestWith([['lt' => 20]])]
    public function test_exception_is_thrown_when_one_of_validations_fails(array $parameters): void
    {
        $this->expectException(QueryBuilderValidationException::class);

        $parameters = new ParameterCollection($parameters);

        $validator = new CompoundValidator(
            new AllOfValidator(['field']),
            new OneOfValidator(['gt', 'lt'])
        );

        $validator->validate($parameters);
    }

    #[TestWith([['field' => 'age', 'gt' => 10]])]
    #[TestWith([['field' => 'age', 'gte' => 10]])]
    #[TestWith([['field' => 'age', 'lt' => 20]])]
    #[TestWith([['field' => 'age', 'lte' => 20]])]
    #[TestWith([['field' => 'age', 'gt' => 10, 'lt' => 20]])]
    #[TestWith([['field' => 'age', 'gte' => 10, 'lte' => 20]])]
    public function test_exception_is_not_thrown_when_all_validations_succeed(array $parameters): void
    {
        $parameters = new ParameterCollection($parameters);

        $validator = new CompoundValidator(
            new AllOfValidator(['field']),
            new OneOfValidator(['gt', 'lt', 'gte', 'lte'])
        );

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
