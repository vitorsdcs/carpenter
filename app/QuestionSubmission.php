<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionSubmission extends BaseModel
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'question', 'duration',
    ];

    protected $appends = [
        'feedback_correct', 'feedback_incorrect',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'originalQuestion', 'question_id', 'exam_submission_id', 'duration', 'deleted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public static $rules = [
        'question' => 'required|max:65535',
        'duration' => 'required|integer|max:65535',
    ];

    public function originalQuestion()
    {
        return $this->belongsTo('App\Question', 'question_id');
    }

    public function examSubmission()
    {
        return $this->belongsTo('App\ExamSubmission');
    }

    public function options()
    {
        return $this->hasMany('App\QuestionOptionSubmission');
    }

    public function questionSelections()
    {
        return $this->hasMany('App\QuestionSelection');
    }

    public function getFeedbackCorrectAttribute()
    {
        return $this->originalQuestion->feedback_correct;
    }

    public function getFeedbackIncorrectAttribute()
    {
        return $this->originalQuestion->feedback_incorrect;
    }

    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->get()->first();
    }

    public function chosenOption()
    {
        return $this->options()->where('chosen', true)->get()->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($questions) {
            $questions->options()->delete();
        });
    }
}
