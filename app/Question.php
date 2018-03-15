<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'question', 'difficulty', 'randomize', 'feedback_correct', 'feedback_incorrect', 'category_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'exam_id', 'category_id', 'deleted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public static $rules = [
        'question' => 'required|max:65535',
        'options' => 'has_one_correct',
        'category_id' => 'exists:categories,id',
        'difficulty' => 'is_valid_difficulty',
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function exams()
    {
        return $this->belongsToMany('App\Exam');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function options()
    {
        return $this->hasMany('App\QuestionOption');
    }

    public function questionSubmissions()
    {
        return $this->hasMany('App\QuestionSubmission');
    }

    public static function all($columns = [])
    {
        return self::where('client_id', Client::getClientId())->with('category', 'options')->get();
    }

    public static function find($questionId)
    {
        return self::where('client_id', Client::getClientId())->with('category', 'options')->find($questionId);
    }

    public static function findOrFail($questionId)
    {
        return self::where('client_id', Client::getClientId())->with('category', 'options')->findOrFail($questionId);
    }

    public function optionsForSubmission()
    {
        if ($this->randomize) {
            return $this->options->shuffle();
        }
        return $this->options->sortBy(function($option) { return $option->answer; });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($question) {
            $question->options()->delete();
        });
    }
}
