<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Time;

class TimeController extends Controller
{
    public function index()
    {
        $times = Time::orderBy('jikan')->get();
        return view('admin.time.index')
                ->with('times', $times);
    }

    public function create()
    {
        return view('admin.time.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jikan' => [
                'required',
                'max:100',
                Rule::unique('times'),
            ],
        ]);

        $time = new Time();
        $time->jikan = $request->jikan;
        if (isset($request->default_jikan)) {
            // デフォルト活動時間が変更された
            $prev_default = Time::where('default_jikan', '=', 1)->first();
            $prev_default->default_jikan = 0;
            $prev_default->save();
            $time->default_jikan = 1;
        } else {
            $time->default_jikan = 0;
        }
        $time->save();
        return redirect()->route('admin.time')
            ->with('success', "活動時間情報を登録しました");
    }

    public function edit($tid)
    {
        $time = Time::where('id', '=', $tid)->first();
        if(!$time) {
            return redirect('/admin/time/')->with('error', "そのような活動時間がありません");
        }
        return view('admin.time.edit')
                ->with('time', $time);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'jikan' => [
                'required',
                'max:100',
                Rule::unique('times')->ignore($request->tid),
            ],
        ]);


        $time = Time::where('id', '=', $request->tid)->first();
        if (!$time) {
            return redirect('/admin/time/')->with('error', "そのような活動時間がありません");
        }
        $time->jikan = $request->jikan;
        if ($time->default_jikan == 0 && isset($request->default_jikan)) {
            // デフォルト活動時間が変更された
            $prev_default = Time::where('default_jikan', '=', 1)->first();
            if ($prev_default) {
                $prev_default->default_jikan = 0;
                $prev_default->save();
            }
            $time->default_jikan = 1;
        }

        $time->save();
        return redirect()->route('admin.time')
            ->with('success', "活動時間情報を更新しました");
    }

    public function delete($tid)
    {
        dd("time delete");
    }
}
