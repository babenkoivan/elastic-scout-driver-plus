<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;

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

    public function validate(Collection $parameters): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($parameters);
        }
    }
}
