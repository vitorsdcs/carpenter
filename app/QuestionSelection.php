<?php

namespace App;

use Carbon\Carbon;

class QuestionSelection extends BaseModel
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'question_id', 'option_id', 'date',
    ];

    protected $dates = [
        'date',
    ];

    protected $hidden = [
        'question_id', 'option_id', 'exam_submission_id',
    ];

    public function question()
    {
        return $this->belongsTo('App\QuestionSubmission', 'question_id');
    }

    public function option()
    {
        return $this->belongsTo('App\QuestionOptionSubmission', 'option_id');
    }

    public function examSubmission()
    {
        return $this->belongsTo('App\ExamSubmission');
    }

    public function setQuestionIdAttribute($value)
    {
        $this->attributes['question_submission_id'] = $value;
    }

    public function setOptionIdAttribute($value)
    {
        $this->attributes['question_option_submission_id'] = $value;
    }

    public function getDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = Carbon::parse($date)->toDateTimeString();
    }
}
