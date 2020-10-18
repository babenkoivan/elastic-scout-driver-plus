<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\CompoundValidator;
use ElasticScoutDriverPlus\Builders\QueryParameters\Validators\OneOfValidator;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\CompoundValidator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\OneOfValidator
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

        $parameters = new Collection($parameters);

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
        $parameters = new Collection($parameters);

        $validator = new CompoundValidator(
            new AllOfValidator(['field']),
            new OneOfValidator(['gt', 'lt', 'gte', 'lte'])
        );

        $validator->validate($parameters);

        $this->assertTrue(true);
    }
}
