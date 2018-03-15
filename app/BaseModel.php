<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTime;

class BaseModel extends Model
{
    public function getCreatedAtAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function getUpdatedAtAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }

    public function getDeletedAtAttribute($date)
    {
        return $date ? Carbon::parse($date)->toAtomString() : null;
    }
}
