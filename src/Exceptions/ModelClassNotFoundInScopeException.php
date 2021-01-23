<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Exceptions;

use RuntimeException;

final class ModelClassNotFoundInScopeException extends RuntimeException
{
    public function __construct(string $modelClass)
    {
        parent::__construct(sprintf(
            '%s is not found in the model scope',
            $modelClass
        ));
    }
}
