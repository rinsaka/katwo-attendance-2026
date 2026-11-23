<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Time;

class TimeController extends Controller
{
    public function index()
    {
        $times = Time::orderBy('jikan')->get();
        return view('admin.time.index')
                ->with('times', $times);
    }
}
