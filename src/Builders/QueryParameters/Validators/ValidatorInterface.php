<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

interface ValidatorInterface
{
    public function validate(Collection $parameters): void;
}
