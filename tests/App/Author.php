<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

/**
 * @property int    $id
 * @property string $name
 * @property string $last_name
 * @property string $phone_number
 * @property string $email
 */
final class Author extends Model
{
    use Searchable;
    use CustomSearch;

    public $timestamps = false;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * @return array
     */
    public function toSearchableArray()
    {
        return Arr::except($this->toArray(), [$this->getKeyName()]);
    }
}
