<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Carbon\Carbon;
use ElasticScoutDriverPlus\ComplexSearch;
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
class Book extends Model
{
    use Searchable, ComplexSearch, SoftDeletes;

    public $timestamps = false;

    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'published',
    ];

    protected $dateFormat = 'Y-m-d';

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
