<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\QueryParameters\Collection;

interface ValidatorInterface
{
    public function validate(Collection $parameters): void;
}
