<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Models\Task::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence,
        'project_id' => factory('App\Models\Project'),
        'completed' => false
    ];
});
