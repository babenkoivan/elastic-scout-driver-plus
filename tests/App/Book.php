<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int    $id
 * @property int    $author_id
 * @property string $title
 * @property string $description
 * @property float  $price
 * @property array  $tags
 * @property Carbon $published
 * @property Carbon $deleted_at
 * @property Author $author
 */
class Book extends Model
{
    use SoftDeletes;

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

    /**
     * @return string
     */
    public function shardRouting()
    {
        return $this->author->name;
    }

    /**
     * @return array
     */
    public function searchableWith()
    {
        return ['author'];
    }
}
