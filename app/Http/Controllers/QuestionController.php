<?php

namespace App\Http\Controllers;

use App\Client;
use App\Question;
use App\QuestionOption;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->has('per_page') ? (int) $request->input('per_page') : 20;

        if ($request->has('question')) {
            return response()->json(Question::with('category')->where('question', 'like', $request->input('question') . '%')->orderBy('question')->paginate($perPage));
        }
        if ($request->has('filter')) {
            return response()->json(Question::with('category')->where($request->input('filter'))->orderBy('question')->paginate($perPage));
        }

        return response()->json(Question::with('category')->orderBy('question')->paginate($perPage));
    }

    public function show($questionId)
    {
        $question = Question::findOrFail($questionId);
        return response()->json($question);
    }

    public function store(Request $request)
    {
        $this->validate($request, Question::$rules);

        $client = Client::getClient();
        $question = $client->questions()->create($request->all());

        if ($request->has('options')) {
            $question->options()->createMany($request->options);
        }

        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, $questionId)
    {
        $this->validate($request, Question::$rules);

        $question = Question::findOrFail($questionId);
        $question->fill($request->all());
        $question->save();

        $savedOptions = [];

        if ($request->has('options')) {
            foreach ($request->options as $option) {
                $optionModel = QuestionOption::firstOrNew(['id' => isset($option['id']) ? $option['id'] : 0]);
                $optionModel->fill($option);
                $optionModel = $question->options()->save($optionModel);
                $savedOptions[] = $optionModel->id;
            }
        }

        QuestionOption
            ::where('question_id', $question->id)
            ->whereNotIn('id', $savedOptions)
            ->each(function($option) {
                $option->delete();
            });

        return response()->json(['success' => true]);
    }

    public function destroy($questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->delete();
        return response()->json(null, 204);
    }
}
