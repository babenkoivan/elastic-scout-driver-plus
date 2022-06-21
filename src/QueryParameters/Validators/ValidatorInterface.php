<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Validators;

use Elastic\ScoutDriverPlus\QueryParameters\ParameterCollection;

interface ValidatorInterface
{
    public function validate(ParameterCollection $parameters): void;
}
