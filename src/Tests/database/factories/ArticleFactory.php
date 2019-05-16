<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(ArinaSystems\LocalizedSlug\Tests\App\Models\Article::class, function (Faker $faker) {
    return [
        'title' => json_encode([
            'ar' => $faker->sentence,
            'en' => $faker->sentence,
        ]),
        'body' => json_encode([
            'ar' => $faker->paragraph,
            'en' => $faker->paragraph,
        ]),
    ];
});
