<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Factories;

use Illuminate\Database\Eloquent\Model;

interface LazyModelFactoryInterface
{
    /**
     * @param  mixed  $id
     * @return Model|null
     */
    public function makeById($id): ?Model;
}
