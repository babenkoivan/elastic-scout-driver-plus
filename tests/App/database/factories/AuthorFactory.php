<?php declare(strict_types=1);

use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Author::class, static function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone_number' => $faker->unique()->e164PhoneNumber,
        'email' => $faker->unique()->email,
    ];
});

$factory->afterCreatingState(Author::class, 'has_books', static function (Author $author, Faker $faker) {
    $books = factory(Book::class, rand(1, 10))->make();
    $author->books()->saveMany($books);
});
