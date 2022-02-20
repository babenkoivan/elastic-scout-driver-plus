<?php declare(strict_types=1);

use ElasticScoutDriverPlus\Tests\App\Store;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Store::class, static function (Faker $faker) {
    return [
        'name' => $faker->name,
        'lat' => $faker->randomNumber(),
        'lon' => $faker->randomNumber(),
    ];
});
