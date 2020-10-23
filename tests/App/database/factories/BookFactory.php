<?php declare(strict_types=1);

use Carbon\Carbon;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Book;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Book::class, static function (Faker $faker) {
    return [
        'title' => $faker->sentence(5),
        'description' => $faker->realText(),
        'price' => $faker->randomNumber(3),
        'published' => Carbon::createFromFormat('Y-m-d', $faker->date('Y-m-d')),
        'tags' => $faker->words(random_int(1, 5)),
    ];
});

$factory->afterMakingState(Book::class, 'belongs_to_author', static function (Book $book, Faker $faker) {
    $book->author_id = factory(Author::class)->create()->id;
});
