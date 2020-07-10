<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use ElasticScoutDriverPlus\CustomSearch;
use ElasticScoutDriverPlus\Searchable\Aggregator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mixed extends Aggregator
{
    use CustomSearch;
    use SoftDeletes;

    protected $models = [
        Article::class,
        Book::class,
    ];

    public function searchableAs(): string
    {
        return 'mixed';
    }
}
