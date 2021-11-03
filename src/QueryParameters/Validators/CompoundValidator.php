<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

final class CompoundValidator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(ValidatorInterface ...$validators)
    {
        $this->validators = $validators;
    }

    public function validate(ParameterCollection $parameters): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($parameters);
        }
    }
}
