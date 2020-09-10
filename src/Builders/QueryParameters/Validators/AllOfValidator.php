<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\QueryParameters\Validators;

use ElasticScoutDriverPlus\Builders\QueryParameters\Collection;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;

final class AllOfValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private $required;

    public function __construct(array $required)
    {
        $this->required = $required;
    }

    public function validate(Collection $parameters): void
    {
        $isInvalid = $parameters->only($this->required)->excludeEmpty()->count() !== count($this->required);

        if ($isInvalid) {
            throw new QueryBuilderException(
                'All required fields have to be specified: ' .
                implode(', ', $this->required)
            );
        }
    }
}
