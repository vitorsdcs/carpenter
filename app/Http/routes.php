<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers', 'middleware' => 'auth'], function($app) {
    $app->get('/settings', 'SettingController@index');
    $app->get('/settings/{setting}', 'SettingController@show');
    $app->post('/settings', 'SettingController@store');
    $app->put('/settings/{setting}', 'SettingController@update');
    $app->delete('/settings/{setting}', 'SettingController@destroy');

    $app->get('/clients', 'ClientController@index');
    $app->get('/clients/{client}', 'ClientController@show');
    $app->post('/clients', 'ClientController@store');
    $app->put('/clients/{client}', 'ClientController@update');
    $app->delete('/clients/{client}', 'ClientController@destroy');

    $app->get('/categories', 'CategoryController@index');
    $app->get('/categories/{category}', 'CategoryController@show');
    $app->post('/categories', 'CategoryController@store');
    $app->put('/categories/{category}', 'CategoryController@update');
    $app->delete('/categories/{category}', 'CategoryController@destroy');

    $app->get('/exams', 'ExamController@index');
    $app->get('/exams/{exam}', 'ExamController@show');
    $app->post('/exams', 'ExamController@store');
    $app->put('/exams/{exam}', 'ExamController@update');
    $app->delete('/exams/{exam}', 'ExamController@destroy');

    $app->get('/questions', 'QuestionController@index');
    $app->get('/questions/{question}', 'QuestionController@show');
    $app->post('/questions', 'QuestionController@store');
    $app->put('/questions/{question}', 'QuestionController@update');
    $app->delete('/questions/{question}', 'QuestionController@destroy');

    $app->get('/exams/{exam}/questions', 'ExamController@listQuestions');
    $app->post('/exams/{exam}/questions/{question}', 'ExamController@attachQuestion');
    $app->delete('/exams/{exam}/questions/{question}', 'ExamController@detachQuestion');

    $app->post('/{examinableType}/{examinableId}/exams/{exam}/submissions', 'ExamSubmissionController@create');
    $app->get('/exams/{exam}/submissions/{submission}', 'ExamSubmissionController@show');
    $app->put('/exams/{exam}/submissions/{submission}', 'ExamSubmissionController@submit');
    $app->delete('/exams/{exam}/submissions/{submission}', 'ExamSubmissionController@destroy');
});