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

$factory->define(App\Client::class, function ($faker) {
    return [
        'client_id' => $faker->md5,
        'title' => $faker->company,
    ];
});

$factory->define(App\Setting::class, function ($faker) {
    return [
        'client_id' => function () {
            return factory(App\Client::class)->create()->client_id;
        },
        'name' => $faker->userName,
        'value' => $faker->userName,
    ];
});

$factory->define(App\User::class, function ($faker) {
    return [
        'client_id' => function () {
            return factory(App\Client::class)->create()->client_id;
        },
        'name' => $faker->name,
        'email' => $faker->email,
        'picture' => $faker->md5,
    ];
});

$factory->define(App\Category::class, function ($faker) {
    return [
        'client_id' => function () {
            return Auth::check() ? App\Client::getClientId() : factory(App\Client::class)->create()->client_id;
        },
        'name' => $faker->word,
    ];
});

$factory->define(App\Exam::class, function ($faker) {
    return [
        'client_id' => function () {
            return Auth::check() ? App\Client::getClientId() : factory(App\Client::class)->create()->client_id;
        },
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'duration' => $faker->numberBetween(1, 15) * 10,
        'attempts' => 99,
        'size' => 10,
        'cutoff' => 50,
        'start_date' => null,
        'end_date' => null,
    ];
});

$factory->define(App\ExamCategoryFilter::class, function ($faker) {
    return [
        'count' => $faker->numberBetween(1, 3),
    ];
});

$factory->define(App\Question::class, function ($faker) {
    $difficulty = array_merge(array(null), App\DifficultyLevel::getKeys());
    return [
        'client_id' => function () {
            return Auth::check() ? App\Client::getClientId() : factory(App\Client::class)->create()->client_id;
        },
        'question' => $faker->paragraph,
        'category_id' => function () {
            $category = App\Category::inRandomOrder()->first();
            return $category ? $category->id : factory(App\Category::class)->create()->id;
        },
        'difficulty' => $difficulty[array_rand($difficulty)],
        'feedback_correct' => $faker->sentence,
        'feedback_incorrect' => $faker->sentence,
    ];
});

$factory->define(App\QuestionOption::class, function ($faker) {
    return [
        'answer' => $faker->paragraph,
        'is_correct' => false,
        'question_id' => function () {
            return factory(App\Question::class)->create()->id;
        }
    ];
});
