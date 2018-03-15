<?php

namespace App;

class Setting extends BaseModel
{
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'name', 'value',
    ];

    protected $hidden = [
        'client_id', 'created_at', 'updated_at',
    ];

    public static $rules = [
        'name' => 'required|max:255',
        'value' => 'required|max:255',
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public static function all($columns = [])
    {
        return self::where('client_id', Client::getClientId())->get();
    }

    public static function find($name)
    {
        return self::where(['client_id' => Client::getClientId(), 'name' => $name])->first();
    }

    public static function findOrFail($name)
    {
        return self::where(['client_id' => Client::getClientId(), 'name' => $name])->firstOrFail();
    }
}
