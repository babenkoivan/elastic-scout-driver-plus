<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

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

    public function validate(ParameterCollection $parameters): void
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
