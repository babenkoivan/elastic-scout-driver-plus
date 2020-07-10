<?php
declare(strict_types=1);

use Carbon\Carbon;
use ElasticScoutDriverPlus\Tests\App\Author;
use ElasticScoutDriverPlus\Tests\App\Article;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory  */
$factory->define(Article::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(5),
        'description' => $faker->realText(),
        'price' => $faker->randomNumber(3),
        'published' => Carbon::createFromFormat('Y-m-d', $faker->date('Y-m-d')),
    ];
});

$factory->afterMakingState(Article::class, 'belongs_to_author', function (Article $article, Faker $faker) {
    $article->author_id = factory(Author::class)->create()->id;
});
