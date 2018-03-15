<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOptionSubmission extends BaseModel
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'answer', 'is_correct',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'question_submission_id', 'question_option_id', 'chosen', 'deleted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public static $rules = [
        'answer' => 'required|max:65535',
    ];

    public function option()
    {
        return $this->belongsTo('App\QuestionOption', 'question_option_id');
    }

    public function questionSubmission()
    {
        return $this->belongsTo('App\QuestionSubmission');
    }

    public function questionSelections()
    {
        return $this->hasMany('App\QuestionSelection');
    }
}
