<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExamTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function testExamIndex()
    {
        $response = $this->call('GET', '/v1/exams');

        $this->assertEquals(200, $response->status());
    }

    public function testExamRead()
    {
        $exam = factory('App\Exam')->create();

        $response = $this->call('GET', "/v1/exams/$exam->id");

        $this->assertEquals(200, $response->status());
    }

    public function testExamCreation()
    {
        $exam = factory('App\Exam')->make()->toArray();

        $response = $this->call('POST', '/v1/exams', $exam);

        $this->assertEquals(201, $response->status());
    }

    public function testExamCreationIncomplete()
    {
        $exam = factory('App\Exam')->make()->toArray();
        unset($exam['title']);

        $response = $this->call('POST', '/v1/exams', $exam);

        $this->assertEquals(422, $response->status());
    }

    public function testExamCreationWithUnprocessableDescription()
    {
        $exam = factory('App\Exam')->make(['description' => str_repeat('a', 65536)])->toArray();

        $response = $this->call('POST', '/v1/exams', $exam);

        $this->assertEquals(422, $response->status());
    }

    public function testExamCreationWithUnprocessableDuration()
    {
        $exam = factory('App\Exam')->make(['duration' => 0])->toArray();

        $response = $this->call('POST', '/v1/exams', $exam);

        $this->assertEquals(422, $response->status());
    }

    public function testExamUpdate()
    {
        $exam = factory('App\Exam')->create();
        $updatedExam = factory('App\Exam')->make()->toArray();

        $response = $this->call('PUT', "/v1/exams/$exam->id", $updatedExam);

        $this->assertEquals(200, $response->status());
    }

    public function testExamUpdateIncomplete()
    {
        $exam = factory('App\Exam')->create();
        $updatedExam = factory('App\Exam')->make()->toArray();
        unset($updatedExam['title']);

        $response = $this->call('PUT', "/v1/exams/$exam->id", $updatedExam);

        $this->assertEquals(422, $response->status());
    }

    public function testExamDeletion()
    {
        $exam = factory('App\Exam')->create();

        $response = $this->call('DELETE', "/v1/exams/$exam->id");

        $this->assertEquals(204, $response->status());
    }

    public function testExamTargeting()
    {
        $anotherUser = factory('App\User')->create();
        $exam = factory('App\Exam')->create(['client_id' => $anotherUser->client_id]);
        $updatedExam = factory('App\Exam')->make()->toArray();

        $response = $this->call('GET', "/v1/exams/$exam->id");
        $this->assertEquals(404, $response->status());

        $response = $this->call('PUT', "/v1/exams/$exam->id", $updatedExam);
        $this->assertEquals(404, $response->status());

        $response = $this->call('DELETE', "/v1/exams/$exam->id");
        $this->assertEquals(404, $response->status());
    }
}
