<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'main_background_color',
            'value' => '#ededed',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'main_foreground_color',
            'value' => '#e60000',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'status_disabled_color',
            'value' => '#800000',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'status_foreground_color',
            'value' => '#ffffff',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'approved_color',
            'value' => '#9da706',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'failed_color',
            'value' => '#9d3867',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'exam_submission_time_limit_hours',
            'value' => '24',
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'show_questions_feedback',
            'value' => true,
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'required_questions',
            'value' => true,
        ]);

        factory(App\Setting::class)->create([
            'client_id' => 'canaleducacao',
            'name' => 'questions_not_answered_text',
            'value' => 'Você deve responder as questões abaixo para poder finalizar o exame:',
        ]);
    }
}
