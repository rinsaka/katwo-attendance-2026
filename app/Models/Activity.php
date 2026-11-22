<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // One to Many の Many 側
    public function place()
    {
        return $this->belongsTo('App\Models\Place')->withDefault();
    }

    // One to Many の Many 側
    public function time()
    {
        return $this->belongsTo('App\Models\Time')->withDefault();
    }

    // One to Many の One 側
    public function attendances()
    {
        return $this->hasMany('App\Models\Attendance');
    }

    // One to One
    public function Menu()
    {
        return $this->hasOne('App\Models\Menu');
    }
}
