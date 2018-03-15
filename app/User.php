<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    protected $fillable = [
        'name', 'client_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function examSubmission()
    {
        return $this->hasMany('App\ExamSubmission');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
