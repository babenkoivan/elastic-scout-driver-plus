<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\App;

/**
 * @property int    $id
 * @property string $name
 * @property float  $lat
 * @property float  $lon
 */
final class Store extends Model
{
    protected $hidden = ['lat', 'lon'];

    /**
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = parent::toSearchableArray();

        $searchable['location'] = [
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];

        return $searchable;
    }
}
