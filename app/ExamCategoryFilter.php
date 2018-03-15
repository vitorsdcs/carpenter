<?php

namespace App;

class ExamCategoryFilter extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'category_id', 'count',
    ];

    protected $hidden = [
        'exam_id', 'category_id',
    ];

    public static $rules = [
        'category_id' => 'required|exists:categories,id',
        'count' => 'required|numeric|min:1',
    ];

    public function exam()
    {
        return $this->belongsTo('App\Exam');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
