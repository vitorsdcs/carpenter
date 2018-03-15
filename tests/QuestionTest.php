<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Question;

class QuestionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function testQuestionIndex()
    {
        $question = factory('App\Question')->create();

        $response = $this->call('GET', "/v1/questions");

        $this->assertEquals(200, $response->status());
    }

    public function testQuestionRead()
    {
        $question = factory('App\Question')->create();

        $response = $this->call('GET', "/v1/questions/$question->id");

        $this->assertEquals(200, $response->status());
    }

    public function testQuestionCreation()
    {
        $question = factory('App\Question')->make();

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(201, $response->status());
    }

    public function testUncategorizedQuestionCreation()
    {
        $question = factory('App\Question')->make(['category_id' => null]);

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(201, $response->status());
    }

    public function testQuestionCreationWithInvalidDifficultyLevel()
    {
        $question = factory('App\Question')->make(['difficulty' => 'xpto']);

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionCreationWithOptions()
    {
        $question = self::makeQuestionWithOptions(10);
        $options = $question->options;

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $question = Question::all()->last();

        $this->assertEquals(201, $response->status());
        $this->seeInDatabase('questions', ['question' => $question->question]);
    }

    public function testQuestionOptionRandomization()
    {
        $question = self::makeQuestionWithOptions(10);

        $this->call('POST', "/v1/questions", $question->toArray());

        $question = Question::all()->last();
        $options = $question->optionsForSubmission()->toArray();
        $sortedOptions = $options;
        sort($sortedOptions);

        $this->assertTrue($options != $sortedOptions);

        $question = self::makeQuestionWithOptions(10, false);

        $this->call('POST', "/v1/questions", $question->toArray());

        $question = Question::all()->last();
        $options = $question->optionsForSubmission()->toArray();
        $sortedOptions = $options;
        sort($sortedOptions);

        $this->assertTrue($options == $sortedOptions);
    }

    public function testQuestionCreationIncomplete()
    {
        $question = factory('App\Question')->create();
        unset($question->question);

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionCreationUnprocessable()
    {
        $question = factory('App\Question')->make(['question' => str_repeat('a', 65536)]);

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionCreationWithMultipleCorrectAnswers()
    {
        $question = factory('App\Question')->make();
        $options = [
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => true])->toArray(),
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => true])->toArray(),
        ];
        $question->options = $options;

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionCreationWithoutCorrectAnswers()
    {
        $question = factory('App\Question')->make();
        $options = [
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->make(['question_id' => null, 'is_correct' => false])->toArray(),
        ];
        $question->options = $options;

        $response = $this->call('POST', "/v1/questions", $question->toArray());

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionUpdate()
    {
        $question = factory('App\Question')->create();
        $updatedQuestion = factory('App\Question')->make(['exam_id' => $question->exam_id])->toArray();

        $response = $this->call('PUT', "/v1/questions/$question->id", $updatedQuestion);

        $this->assertEquals(200, $response->status());
    }

    public function testQuestionUpdateIncomplete()
    {
        $question = factory('App\Question')->create();
        $updatedQuestion = factory('App\Question')->make(['exam_id' => $question->exam_id])->toArray();
        unset($updatedQuestion['question']);

        $response = $this->call('PUT', "/v1/questions/$question->id", $updatedQuestion);

        $this->assertEquals(422, $response->status());
    }

    public function testQuestionUpdateWithAdditionalOptions()
    {
        $question = factory('App\Question')->create();
        $options = [
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => true])->toArray(),
        ];
        $newOptions = [
            factory('App\QuestionOption')->make(['question_id' => $question->id, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->make(['question_id' => $question->id, 'is_correct' => false])->toArray(),
        ];
        $options = array_merge($options, $newOptions);
        $question->options = $options;

        $response = $this->call('PUT', "/v1/questions/$question->id", $question->toArray());

        $this->assertEquals(200, $response->status());
    }

    public function testQuestionUpdateWithRemovedOptions()
    {
        $question = factory('App\Question')->create();
        $options = [
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => true])->toArray(),
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => false])->toArray(),
            factory('App\QuestionOption')->create(['question_id' => $question->id, 'is_correct' => false])->toArray(),
        ];
        $question->options = array_slice($options, 0, 2);

        $response = $this->call('PUT', "/v1/questions/$question->id", $question->toArray());

        $question->load('options');

        $this->assertEquals(200, $response->status());
        $this->assertEquals(2, count($question->options));
    }

    public function testExamQuestionAttachment()
    {
        $exam = factory('App\Exam')->create();
        $question = factory('App\Question')->create();

        $response = $this->call('POST', "/v1/exams/$exam->id/questions/$question->id");

        $this->assertEquals(201, $response->status());
    }

    public function testExamQuestionAttachmentMultiple()
    {
        $exam = factory('App\Exam')->create();
        $exam2 = factory('App\Exam')->create();
        $question = factory('App\Question')->create();

        $response = $this->call('POST', "/v1/exams/$exam->id/questions/$question->id");
        $this->assertEquals(201, $response->status());

        $response = $this->call('POST', "/v1/exams/$exam2->id/questions/$question->id");
        $this->assertEquals(201, $response->status());
    }

    public function testExamQuestionDetachment()
    {
        $exam = factory('App\Exam')->create();
        $question = factory('App\Question')->create();

        $response = $this->call('DELETE', "/v1/exams/$exam->id/questions/$question->id");

        $this->assertEquals(204, $response->status());
    }

    public function testQuestionDeletion()
    {
        $question = factory('App\Question')->create();

        $response = $this->call('DELETE', "/v1/questions/$question->id");

        $this->assertEquals(204, $response->status());
    }

    private static function makeQuestionWithOptions($size, $randomize = true)
    {
        $question = factory('App\Question')->make(['randomize' => $randomize]);
        $correctQuestion = rand(0, $size - 1);

        for ($i = 0; $i < $size; $i++) {
            $options[] = factory('App\QuestionOption')
                ->make(['question_id' => null, 'is_correct' => ($i == $correctQuestion)])
                ->toArray();
        }

        $question->options = $options;
        return $question;
    }
}
