<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\QueryParameters\Validators;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;
use ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator;
use ElasticScoutDriverPlus\QueryParameters\Validators\CompoundValidator;
use ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Validators\CompoundValidator
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\OneOfValidator
 */
final class CompoundValidatorTest extends TestCase
{
    public function invalidParametersDataProvider(): array
    {
        return [
            [['field' => 'age']],
            [['gt' => 10]],
            [['lt' => 20]],
        ];
    }

    public function validParametersDataProvider(): array
    {
        return [
            [['field' => 'age', 'gt' => 10]],
            [['field' => 'age', 'gte' => 10]],
            [['field' => 'age', 'lt' => 20]],
            [['field' => 'age', 'lte' => 20]],
            [['field' => 'age', 'gt' => 10, 'lt' => 20]],
            [['field' => 'age', 'gte' => 10, 'lte' => 20]],
        ];
    }

    /**
     * @dataProvider invalidParametersDataProvider
     */
    public function test_exception_is_thrown_when_one_of_validations_fails(array $parameters): void
    {
        $this->expectException(QueryBuilderException::class);

        $parameters = new ParameterCollection($parameters);

        $validator = new CompoundValidator(
            new AllOfValidator(['field']),
            new OneOfValidator(['gt', 'lt'])
        );

        $validator->validate($parameters);
    }

    /**
     * @dataProvider validParametersDataProvider
     */
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
