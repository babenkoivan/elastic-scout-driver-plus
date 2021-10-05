<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\QueryParameters\Validators;

use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\QueryParameters\ParameterCollection;

final class OneOfValidator implements ValidatorInterface
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
        $isInvalid = $parameters->only($this->required)->excludeEmpty()->count() === 0;

        if ($isInvalid) {
            throw new QueryBuilderException(
                'At least one of the following parameters has to be specified: ' .
                implode(', ', $this->required)
            );
        }
    }
}
