<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOption extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'answer', 'is_correct',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'question_id', 'deleted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public static $rules = [
        'answer' => 'required|max:65535',
    ];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function questionOptionSubmissions()
    {
        return $this->hasMany('App\QuestionOptionSubmission');
    }
}
