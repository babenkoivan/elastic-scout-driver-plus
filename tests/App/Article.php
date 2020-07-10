<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Carbon\Carbon;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $description
 * @property float $price
 * @property Carbon $published
 * @property Carbon $deleted_at
 */
class Article extends Model
{
    use Searchable, CustomSearch, SoftDeletes;

    public $timestamps = false;

    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'published',
    ];

    protected $casts = [
        'published' => 'date:Y-m-d',
    ];

    /**
     * {@inheritDoc}
     */
    public function toSearchableArray()
    {
        return Arr::except($this->toArray(), [$this->getKeyName()]);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
