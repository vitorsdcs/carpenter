<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\ExamSubmission;
use App\Category;
use Carbon\Carbon;

class ExamSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function testExamSubmissionStart()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $this->assertEquals(201, $response->status());
    }

    public function testExamSubmissionTargeting()
    {
        $anotherUser = factory('App\User')->create();
        $exam = self::examWithQuestions(['client_id' => $anotherUser->client_id]);

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");
        $this->assertEquals(404, $response->status());
    }

    public function testExamSubmissionBeforePeriodOfAvailability()
    {
        $start = Carbon::tomorrow()->startOfDay();
        $end = Carbon::tomorrow()->endOfDay();

        $exam = self::examWithQuestions(['start_date' => $start, 'end_date' => $end]);

        $response = $this->setExpectedException('Error')->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $this->assertEquals(403, $response->status());
    }

    public function testExamSubmissionDuringPeriodOfAvailability()
    {
        $start = Carbon::today()->startOfDay();
        $end = Carbon::today()->endOfDay();

        $exam = self::examWithQuestions(['start_date' => $start, 'end_date' => $end]);

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $this->assertEquals(201, $response->status());
    }

    public function testExamSubmissionAfterPeriodOfAvailability()
    {
        $start = Carbon::yesterday()->startOfDay();
        $end = Carbon::yesterday()->endOfDay();

        $exam = self::examWithQuestions(['start_date' => $start, 'end_date' => $end]);

        $response = $this->setExpectedException('Error')->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $this->assertEquals(403, $response->status());
    }

    public function testExamSubmissionQuestionRandomization()
    {
        $exam = self::examWithQuestions(['randomize' => true]);

        $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();
        $questions = $submission->questions->pluck('question')->toArray();
        $sortedQuestions = $questions;
        sort($sortedQuestions);

        $this->assertTrue($questions != $sortedQuestions);

        $exam = self::examWithQuestions(['randomize' => false]);

        $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();
        $questions = $submission->questions->pluck('question')->toArray();
        $sortedQuestions = $questions;
        sort($sortedQuestions);

        $this->assertTrue($questions == $sortedQuestions);
    }

    public function testExamSubmissionWithCategorizedSelection()
    {
        $count = 2;

        $exam = self::examWithQuestions();

        $category = factory('App\Category')->create();
        self::questionsWithOptions($exam, ['category_id' => $category->id], $count);

        factory('App\ExamCategoryFilter')->create(['exam_id' => $exam->id, 'category_id' => $category->id, 'count' => $count]);

        $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = $exam->fresh()->submissions->last();

        $categories = $submission->questions->pluck('originalQuestion.category.id')->toArray();
        $categories = array_count_values($categories);

        $this->assertTrue(isset($categories[$category->id]) && $categories[$category->id] == $count);
    }

    public function testExamSubmissionResume()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $response = $this->call('GET', "/v1/exams/$exam->id/submissions/$submission->id");

        $this->assertEquals(200, $response->status());
    }

    public function testExamSubmissionSubmit()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $data = [
            'answers' => [],
            'selection' => [],
            'finished_at' => Carbon::now(),
            'cheat' => false,
        ];

        foreach ($submission->questions as $question) {
            $data['answers'][] = [
                'question_id' => $question->id,
                'option_id' => $question->options->random()->id,
            ];
        }

        $response = $this->call('PUT', "/v1/exams/$exam->id/submissions/$submission->id", $data);

        $this->assertEquals(200, $response->status());
    }

    public function testExamSubmissionIncompleteSubmit()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $data = [
            'answers' => [],
            'selection' => [],
            'finished_at' => Carbon::now(),
            'cheat' => false,
        ];

        foreach ($submission->questions as $question) {
            $data['answers'][] = [
                'question_id' => $question->id,
                'option_id' => $question->options->random()->id,
            ];
        }
        $data['answers'] = array_slice($data['answers'], 0, 2);

        $response = $this->call('PUT', "/v1/exams/$exam->id/submissions/$submission->id", $data);

        $this->assertEquals(200, $response->status());
    }

    public function testExamSubmissionMaximumScore()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $data = [
            'answers' => [],
            'selection' => [],
            'finished_at' => Carbon::now(),
            'cheat' => false,
        ];

        foreach ($submission->questions as $question) {
            $data['answers'][] = [
                'question_id' => $question->id,
                'option_id' => $question->correctOption()->id,
            ];
        }

        $response = $this->call('PUT', "/v1/exams/$exam->id/submissions/$submission->id", $data);

        $this->assertEquals(200, $response->status());

        $submission = ExamSubmission::find($submission->id);

        $this->assertEquals(100.00, $submission->score);
    }

    public function testExamSubmissionNonMaximumScore()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $data = [
            'answers' => [],
            'selection' => [],
            'finished_at' => Carbon::now(),
            'cheat' => false,
        ];

        foreach ($submission->questions as $question) {
            $data['answers'][] = [
                'question_id' => $question->id,
                'option_id' => $question->options->random()->id,
            ];
        }

        $response = $this->call('PUT', "/v1/exams/$exam->id/submissions/$submission->id", $data);

        $this->assertEquals(200, $response->status());

        $submission = ExamSubmission::find($submission->id);

        $this->assertTrue($submission->score < 100.00);
    }

    public function testExamSubmissionFeedback()
    {
        $exam = self::examWithQuestions();

        $response = $this->call('POST', "/v1/content/1/exams/$exam->id/submissions");

        $submission = ExamSubmission::all()->last();

        $data = [
            'answers' => [],
            'selection' => [],
            'finished_at' => Carbon::now(),
            'cheat' => false,
        ];

        foreach ($submission->questions as $question) {
            $data['answers'][] = [
                'question_id' => $question->id,
                'option_id' => $question->options->random()->id,
            ];
        }

        $response = $this->call('PUT', "/v1/exams/$exam->id/submissions/$submission->id", $data);

        $response = $this->call('GET', "/v1/exams/$exam->id/submissions/$submission->id");

        $this->assertEquals(200, $response->status());
    }

    public static function examWithQuestions($columns = [], $size = 20)
    {
        $exam = factory(App\Exam::class)->create($columns);
        self::questionsWithOptions($exam, [], $size);
        return $exam;
    }

    public static function questionsWithOptions($exam, $columns = [], $size = 20)
    {
        $questions = factory(App\Question::class, $size)
            ->create($columns)
            ->each([self::class, 'options']);

        foreach ($questions as $question) {
            $exam->questions()->attach($question->id);
        }
    }

    public static function options($question) {
        $options = [true, false, false, false, false];
        shuffle($options);

        foreach ($options as $is_correct) {
            factory(App\QuestionOption::class)->create([
                'question_id' => $question->id,
                'is_correct' => $is_correct,
            ]);
        }
    }
}
