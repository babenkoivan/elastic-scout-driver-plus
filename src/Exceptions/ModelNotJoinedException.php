<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Exceptions;

use Elastic\Transport\Exception\InvalidArgumentException;

final class ModelNotJoinedException extends InvalidArgumentException
{
    public function __construct(string $modelClass)
    {
        parent::__construct(sprintf(
            '%s must be added to search via "join" method',
            $modelClass
        ));
    }
}
