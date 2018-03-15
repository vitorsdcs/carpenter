<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'client_id', 'question_id', 'deleted_at',
    ];

    public static $rules = [
        'name' => 'required|max:255',
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function categoryFilter()
    {
        return $this->hasMany('App\ExamCategoryFilter');
    }

    public static function all($columns = [])
    {
        return self::where('client_id', Client::getClientId())->get();
    }

    public static function find($categoryId)
    {
        return self::where('client_id', Client::getClientId())->find($categoryId);
    }

    public static function findOrFail($categoryId)
    {
        return self::where('client_id', Client::getClientId())->findOrFail($categoryId);
    }
}
