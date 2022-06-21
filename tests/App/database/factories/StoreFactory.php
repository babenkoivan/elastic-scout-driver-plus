<?php declare(strict_types=1);

use Elastic\ScoutDriverPlus\Tests\App\Store;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Store::class, static fn (Faker $faker) => [
    'name' => $faker->name,
    'lat' => $faker->randomNumber(),
    'lon' => $faker->randomNumber(),
]);
