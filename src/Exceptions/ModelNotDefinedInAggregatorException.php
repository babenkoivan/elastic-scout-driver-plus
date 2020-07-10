<?php

declare(strict_types=1);

namespace ElasticScoutDriverPlus\Exceptions;

use RuntimeException;
use Throwable;

final class ModelNotDefinedInAggregatorException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Model not defined in aggregator.';
        }

        parent::__construct($message, $code, $previous);
    }
}
