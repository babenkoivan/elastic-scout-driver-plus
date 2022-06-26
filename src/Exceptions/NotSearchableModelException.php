<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Exceptions;

use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class NotSearchableModelException extends InvalidArgumentException
{
    public function __construct(string $modelClass)
    {
        parent::__construct(sprintf(
            '%s must extend %s class and use %s trait',
            $modelClass,
            Model::class,
            Searchable::class
        ));
    }
}
