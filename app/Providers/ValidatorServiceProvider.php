<?php

namespace App\Providers;

use App\DifficultyLevel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('has_one_correct', function($attribute, $value, $parameters, $validator) {
            $trues = 0;

            array_walk($value, function($v) use (&$trues) {
                $trues = $v['is_correct'] ? $trues + 1 : $trues;
            });

            return $trues == 1;
        });

        Validator::extend('is_valid_difficulty', function($attribute, $value, $parameters, $validator) {
            return in_array($value, DifficultyLevel::getKeys());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
