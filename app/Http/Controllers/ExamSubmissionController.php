<?php

namespace App\Http\Controllers;

use App\Exam;
use App\ExamSubmission;
use App\User;
use App\QuestionSubmission;
use App\QuestionOptionSubmission;
use App\Events\ExamStarted;
use App\Events\ExamSubmitted;
use App\Exceptions\ExamAlreadySubmittedException;
use App\Exceptions\ExamAttemptsReachedException;
use App\Exceptions\ExamAuthorSpoofingException;
use App\Exceptions\ExamNotSubmittedException;
use App\Exceptions\ExamOutOfPeriodOfAvailabilityException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamSubmissionController extends Controller
{
    public function create(Request $request, $examinableType, $examinableId, $examId)
    {
        $exam = Exam::findOrFail($examId);

        if (!$exam->hasAttemptsLeft($request->user(), $examinableType, $examinableId)) {
            throw new ExamAttemptsReachedException;
        }

        $submission = new ExamSubmission();
        $submission->exam()->associate($exam);
        $submission->user()->associate($request->user());
        $submission->examinable_type = $examinableType;
        $submission->examinable_id = $examinableId;
        $submission->started_at = Carbon::now();
        $submission->save();

        foreach ($exam->questionsForSubmission() as $question) {
            $questionSubmission = new QuestionSubmission($question->toArray());
            $questionSubmission->originalQuestion()->associate($question);
            $questionSubmission->examSubmission()->associate($submission);
            $questionSubmission->save();

            foreach ($question->optionsForSubmission() as $option) {
                $optionSubmission = new QuestionOptionSubmission($option->toArray());
                $optionSubmission->option()->associate($option);
                $optionSubmission->questionSubmission()->associate($questionSubmission);
                $optionSubmission->save();
            }
        }

        $submission = ExamSubmission::with('exam', 'user', 'questions.options')->find($submission->id);

        event(new ExamStarted($submission));

        return response()->json($submission, 201);
    }

    public function show($examId, $submissionId)
    {
        $exam = Exam::findOrFail($examId);

        $submission = ExamSubmission::with('questions.options')->findOrFail($submissionId);

        return response()->json($submission);
    }

    public function submit(Request $request, $examId, $submissionId)
    {
        $exam = Exam::findOrFail($examId);

        $submission = ExamSubmission::with('exam', 'user', 'questions.options')->findOrFail($submissionId);

        if ($request->user()->id != $submission->user->id) {
            throw new ExamAuthorSpoofingException;
        }

        if ($submission->isCompleted()) {
            throw new ExamAlreadySubmittedException;
        }

        if ($submission->hasExpired()) {
            $submission->expired = true;
        }

        foreach ($submission->questions as $question) {
            $answerKey = array_search($question->id, array_column($request->answers, 'question_id'));

            if ($answerKey === false) {
                continue;
            }

            $answerId = $request->answers[$answerKey]['option_id'];

            if ($answer = $question->options->find($answerId)) {
                $answer->chosen = true;
                $answer->save();
            }

            $question->is_correct = ($answer) ? $answer->is_correct : false;
            $question->save();

            $submission->score += $question->is_correct ? (100 / $submission->questions->count()) : 0;
        }

        $submission->questionSelections()->createMany($request->selection);
        $submission->finished_at = $request->finished_at;
        $submission->cheat = $request->cheat;
        $submission->save();

        event(new ExamSubmitted($submission));

        return response()->json($submission);
    }

    public function destroy($examId, $submissionId)
    {
        $submission = ExamSubmission::with('exam', 'user', 'questions.options')->findOrFail($submissionId);
        $submission->delete();
        return response()->json(null, 204);
    }
}
