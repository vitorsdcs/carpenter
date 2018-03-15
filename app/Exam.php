<?php

namespace App;

use App\Events\ExamCreated;
use App\Events\ExamUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Exam extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'duration', 'attempts', 'size', 'cutoff', 'randomize',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'client_id', 'deleted_at',
    ];

    protected $casts = [
        'randomize' => 'boolean',
    ];

    public static $rules = [
        'title' => 'required|max:255',
        'description' => 'max:65535',
        'duration' => 'required|integer|min:1',
        'attempts' => 'required|integer|min:1',
        'size' => 'required|numeric|min:1',
        'cutoff' => 'required|numeric',
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function questions()
    {
        return $this->belongsToMany('App\Question');
    }

    public function submissions()
    {
        return $this->hasMany('App\ExamSubmission');
    }

    public function categoryFilter()
    {
        return $this->hasMany('App\ExamCategoryFilter');
    }

    public static function all($columns = [])
    {
        return self::where('client_id', Client::getClientId())->get();
    }

    public static function find($examId)
    {
        return self::where('client_id', Client::getClientId())->with('categoryFilter.category', 'questions.category')->find($examId);
    }

    public static function findOrFail($examId)
    {
        return self::where('client_id', Client::getClientId())->with('categoryFilter.category', 'questions.category')->findOrFail($examId);
    }

    public function getStartDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function getEndDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function startDate()
    {
        return new Carbon($this->start_date);
    }

    public function endDate()
    {
        return new Carbon($this->end_date);
    }

    public function isRestrictedByTime()
    {
        return $this->start_date || $this->end_date;
    }

    public function isWithinPeriodOfAvailability()
    {
        return !$this->isRestrictedByTime() || Carbon::now()->between($this->startDate(), $this->endDate());
    }

    public function remainingAttempts($user, $examinableType, $examinableId)
    {
        $attempted = ExamSubmission
            ::where('exam_id', $this->id)
            ->where('user_id', $user->id)
            ->where('examinable_type', $examinableType)
            ->where('examinable_id', $examinableId)
            ->count();

        return $this->attempts - $attempted;
    }

    public function hasAttemptsLeft($user, $examinableType, $examinableId)
    {
        return $this->remainingAttempts($user, $examinableType, $examinableId) > 0;
    }

    public function questionsForSubmission()
    {
        $questions = new Collection();

        foreach ($this->categoryFilter as $filter) {
            $questionsByCategory = $this->questionsByCategory($filter->category_id);
            $questionsByCategory = $this->questionsChunk($questionsByCategory, $filter->count, $this->randomize);
            $questions = $questions->merge($questionsByCategory);
        }

        $remaining = $this->size - $questions->count();
        $questionsRemaining = $this->questions->diff($questions);
        $questionsRemaining = $this->questionsChunk($questionsRemaining, $remaining, $this->randomize);
        $questions = $questions->merge($questionsRemaining);

        return $questions;
    }

    private function questionsByCategory($categoryId)
    {
        return $this->questions->filter(function($question) use ($categoryId) {
            return $question->category_id == $categoryId;
        });
    }

    private function questionsChunk($questions, $size, $randomize = false)
    {
        if ($randomize) {
            if ($size > $questions->count()) {
                $size = $questions->count();
            }
            return $size == 1 ? new Collection([$questions->random($size)]) : $questions->random($size);
        }

        return $questions->sortBy(function($question) {
            return $question->question;
        })->take($size);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($exam) {
            event(new ExamCreated($exam));
        });

        static::updated(function($exam) {
            event(new ExamUpdated($exam));
        });

        static::deleting(function($exam) {
            $exam->categoryFilter()->delete();
            $exam->questions()->delete();
        });
    }
}
