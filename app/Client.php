<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends BaseModel
{
    use SoftDeletes;

    public $incrementing = false;

    protected $primaryKey = 'client_id';

    protected $guarded = [];

    protected $fillable = [
        'title',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public static $rules = [
        'title' => 'required|max:255',
    ];

    public function exams()
    {
        return $this->hasMany('App\Exam');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function categories()
    {
        return $this->hasMany('App\Category');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function settings()
    {
        return $this->hasMany('App\Setting');
    }

    public static function getClientId()
    {
        return Auth::user()->client_id;
    }

    public static function getClient()
    {
        return self::findOrFail(self::getClientId());
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($client) {
            $client->exams()->delete();
            $client->users()->delete();
        });
    }
}
