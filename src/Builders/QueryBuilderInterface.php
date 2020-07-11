<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders;

interface QueryBuilderInterface
{
    public function buildQuery(): array;
}
