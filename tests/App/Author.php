<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $phone_number
 * @property string $email
 */
final class Author extends Model
{
    public $timestamps = false;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
