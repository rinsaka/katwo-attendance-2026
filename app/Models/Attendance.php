<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // One to Many の Many 側
    public function part()
    {
        return $this->belongsTo('App\Models\Part');
    }

    // One to Many の Many 側
    public function activity()
    {
        return $this->belongsTo('App\Models\Activity');
    }
}
