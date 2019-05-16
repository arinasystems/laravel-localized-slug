<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(ArinaSystems\LocalizedSlug\Tests\App\Models\Blog::class, function (Faker $faker) {
    return [
        'en_title' => $faker->sentence,
        'en_body'  => $faker->paragraph,
        'es_title' => $faker->sentence,
        'es_body'  => $faker->paragraph,
    ];
});
