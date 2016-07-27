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

$factory->define(App\Farm::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
		'openingTime' => $faker->time,
		'closingTime' => $faker->time
	];
});

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10))
    ];
});

$factory->define(App\Geolocation::class, function (Faker\Generator $faker) {
	return [
		'latitude' => $faker->latitude,
		'longitude' => $faker->longitude
	];
});

$factory->define(App\Review::class, function (Faker\Generator $faker) {
	return [
		'comment' => $faker->sentence,
		'vote' => $faker->numberBetween(-1, 1)
	];
});

$factory->define(App\Market::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
		'openingTime' => $faker->time,
		'closingTime' => $faker->time
	];
});