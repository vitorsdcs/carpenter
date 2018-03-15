<?php

namespace App\Http\Controllers;

use App\Exam;
use App\Client;
use App\Question;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->has('per_page') ? (int) $request->input('per_page') : 20;

        if ($request->has('title')) {
            return response()->json(Exam::where('title', 'like', $request->input('title') . '%')->paginate($perPage));
        }
        if ($request->has('filter')) {
            return response()->json(Exam::where($request->input('filter'))->paginate($perPage));
        }

        return response()->json(Exam::paginate($perPage));
    }

    public function show($examId)
    {
        $exam = Exam::findOrFail($examId);
        return response()->json($exam);
    }

    public function store(Request $request)
    {
        $this->validate($request, Exam::$rules);

        $client = Client::getClient();
        $exam = $client->exams()->create($request->all());

        if ($request->has('category_filter')) {
            $exam->categoryFilter()->createMany($request->category_filter);
        }

        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, $examId)
    {
        $this->validate($request, Exam::$rules);

        $exam = Exam::findOrFail($examId);
        $exam->fill($request->all());
        $exam->save();

        $exam->categoryFilter()->delete();

        if ($request->has('category_filter')) {
            $exam->categoryFilter()->createMany($request->category_filter);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($examId)
    {
        $exam = Exam::findOrFail($examId);
        $exam->delete();
        return response()->json(null, 204);
    }

    public function listQuestions(Request $request, $examId)
    {
        $perPage = $request->has('per_page') ? (int) $request->input('per_page') : 20;

        if ($request->has('question')) {
            return response()->json(Exam::findOrFail($examId)->questions()->with('category')->where('question', 'like', $request->input('question') . '%')->orderBy('question')->paginate($perPage));
        }
        if ($request->has('filter')) {
            return response()->json(Exam::findOrFail($examId)->questions()->with('category')->where($request->input('filter'))->orderBy('question')->paginate($perPage));
        }

        return response()->json(Exam::findOrFail($examId)->questions()->with('category')->orderBy('question')->paginate($perPage));
    }

    public function attachQuestion(Request $request, $examId, $questionId)
    {
        $exam = Exam::findOrFail($examId);
        $question = Question::findOrFail($questionId);
        $exam->questions()->attach($question->id);
        return response()->json(['success' => true], 201);
    }

    public function detachQuestion($examId, $questionId)
    {
        $exam = Exam::findOrFail($examId);
        $question = Question::findOrFail($questionId);
        $exam->questions()->detach($question->id);
        return response()->json(null, 204);
    }
}
