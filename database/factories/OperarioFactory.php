<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Operario;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Operario::class, function (Faker $faker) {
    return [
        'nombre' => $faker->nombre,
        'rut' => $faker->unique()->rut,
        'correo' => $faker->unique()->correo,
        'empresa' => $faker->empresa,
        
    ];
});
