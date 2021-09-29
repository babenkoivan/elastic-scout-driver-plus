<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

interface ValidatorInterface
{
    public function validate(ParameterCollection $parameters): void;
}
