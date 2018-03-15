<?php

namespace App\Http\Controllers;

use App\Category;
use App\Client;
use App\Exam;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->has('per_page') ? (int) $request->input('per_page') : 20;

        if ($request->has('name')) {
            return response()->json(Category::where('name', 'like', $request->input('name') . '%')->paginate($perPage));
        }
        if ($request->has('filter')) {
            return response()->json(Category::where($request->input('filter'))->paginate($perPage));
        }

        return response()->json(Category::paginate($perPage));
    }

    public function show(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $this->validate($request, Category::$rules);

        $client = Client::getClient();
        $category = $client->categories()->create($request->all());

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, Category::$rules);

        $category = Category::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $exams = Exam::findMany($category->categoryFilter()->pluck('exam_id'));
        $questions = $category->questions()->get();

        if (!$exams->isEmpty() || !$questions->isEmpty()) {
            return response()->json(['exams' => $exams, 'questions' => $questions], 403);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
