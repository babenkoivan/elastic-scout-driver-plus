<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\QueryParameters\Collection;

/**
 * @covers \ElasticScoutDriverPlus\QueryParameters\Validators\NullValidator
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Collection
 */
final class NullValidator implements ValidatorInterface
{
    public function validate(Collection $parameters): void
    {
    }
}
