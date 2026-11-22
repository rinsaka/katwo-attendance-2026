<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // One to One
    public function activity()
    {
        return $this->belongsTo('App\Models\Activity');
    }
}
