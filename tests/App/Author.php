<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int        $id
 * @property string     $name
 * @property string     $last_name
 * @property string     $phone_number
 * @property string     $email
 * @property Collection $books
 */
final class Author extends Model
{
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
