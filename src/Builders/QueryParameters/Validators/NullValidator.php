<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

/**
 * @covers \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\NullValidator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 */
final class NullValidator implements ValidatorInterface
{
    public function validate(Collection $parameters): void
    {
    }
}
