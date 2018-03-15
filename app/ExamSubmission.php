<?php

namespace App;

use App\Events\ExamSubmissionDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSubmission extends BaseModel
{
    use SoftDeletes;

    public $timestamps = false;

    protected $appends = [
        'approved',
    ];

    protected $dates = [
        'started_at', 'finished_at', 'deleted_at',
    ];

    protected $hidden = [
        'exam_id', 'user_id', 'deleted_at',
    ];

    protected $casts = [
        'cheat' => 'boolean',
        'expired' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo('App\Exam');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function questions()
    {
        return $this->hasMany('App\QuestionSubmission');
    }

    public function questionSelections()
    {
        return $this->hasMany('App\QuestionSelection');
    }

    public function getApprovedAttribute()
    {
        return $this->isCompleted() && !$this->expired && $this->score >= $this->exam->cutoff;
    }

    public function getStartedAtAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function getFinishedAtAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function setFinishedAtAttribute($date)
    {
        $this->attributes['finished_at'] = Carbon::parse($date)->toDateTimeString();
    }

    public function startedAt()
    {
        return new Carbon($this->started_at);
    }

    public function finishedAt()
    {
        return new Carbon($this->finished_at);
    }

    public function isCompleted()
    {
        return (boolean) $this->finished_at;
    }

    public function hasExpired($dt = null)
    {
        $limit = Setting::find('exam_submission_time_limit_hours');
        return $limit && $this->startedAt()->diffInHours($dt) >= $limit->value;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($examSubmission) {
            $examSubmission->questions()->delete();
            event(new ExamSubmissionDeleted($examSubmission));
        });
    }
}
