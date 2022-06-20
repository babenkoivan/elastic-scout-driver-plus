<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use ElasticScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    use Searchable;

    protected $guarded = [];
    public $timestamps = false;

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->attributesToArray();
    }
}
