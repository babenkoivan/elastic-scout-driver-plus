<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Carbon\Carbon;
use ElasticScoutDriverPlus\ShardRouting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int    $id
 * @property int    $author_id
 * @property string $title
 * @property string $description
 * @property float  $price
 * @property Carbon $published
 * @property Carbon $deleted_at
 * @property Author $author
 */
class Book extends Model
{
    use SoftDeletes;
    use ShardRouting;

    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'published',
    ];

    protected $casts = [
        'published' => 'date:Y-m-d',
        'tags' => 'json',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = parent::toSearchableArray();
        $searchable['author'] = $this->author->only(['name', 'phone_number']);

        return $searchable;
    }

    public function getRouting(): string
    {
        return $this->author->name;
    }
}
