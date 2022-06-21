<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Builders;

interface QueryBuilderInterface
{
    public function buildQuery(): array;
}
