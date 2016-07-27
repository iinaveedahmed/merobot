<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

// shop factory
$factory->define(Merobot\Shop::class, function (Faker\Generator $faker) {
    return [
        'height' => 20,
        'width' => 20,
    ];
});

// robot factory
$factory->define(Merobot\Robot::class, function (Faker\Generator $faker) {
    return [
        'shop_id' => 20,
        'x' => 3,
        'y' => 3,
        'heading' => 'S'
    ];
});
