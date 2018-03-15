<?php

use Illuminate\Database\Seeder;

class ExamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Client::class)
            ->create(['client_id' => 'canaleducacao', 'title' => 'Canal Educação'])
            ->each(function($client) {
                factory(App\Category::class, 2)
                    ->create(['client_id' => $client->client_id]);

                factory(App\Exam::class, 5)
                    ->create(['client_id' => $client->client_id])
                    ->each(function($exam) {

                        $questions = factory(App\Question::class, 15)
                            ->create(['client_id' => $exam->client_id])
                            ->each(function($question) {
                                $options = [true, false, false, false, false];
                                shuffle($options);

                                foreach ($options as $is_correct) {
                                    factory(App\QuestionOption::class)->create([
                                        'question_id' => $question->id,
                                        'is_correct' => $is_correct,
                                    ]);
                                }
                            });

                        foreach ($questions as $question) {
                            $exam->questions()->attach($question->id);
                        }
                    });
            });
    }
}
