<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Exceptions;

use InvalidArgumentException;

final class ConnectionMismatchException extends InvalidArgumentException
{
    /**
     * @param array<string> $connectionNames
     */
    public function __construct(array $connectionNames)
    {
        parent::__construct(sprintf(
            'All searches must use the same connection, got %s',
            implode(', ', $connectionNames)
        ));
    }
}
