<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;
use App\Models\Time;
use App\Models\Place;

class DashboardController extends Controller
{
    public function index()
    {
        $activities = Activity::orderBy('act_at')->orderBy('meeting', 'DESC')->get();

        // 新規登録と更新情報を設定
        foreach ($activities as $activity) {
            // 最終更新からの経過時間を分単位で取得する
            $minutes_from_update = (strtotime("now") - strtotime($activity->updated_at)) / 60;
            // 「新規」と「更新」を初期化
            $activity->new = false;
            $activity->update = false;
            // 定数で指定された時間より短ければ「新規」または「更新」を設定する
            if ($minutes_from_update < \Config::get('const.NEW_THRESHOLD')) {
                if ($activity->created_at == $activity->updated_at) {
                    $activity->new = true;
                } else {
                $activity->update = true;
                }
            }
        }

        return view('admin.dashboard')
            ->with('activities', $activities);
    }

    public function edit($aid)
    {
        $activity = Activity::where('id', '=', $aid)->first();
        if(!$activity) {
            return redirect('/admin/dashboard/')->with('error', "そのような活動予定がありません");
        }

        $times = Time::orderBy('jikan')->get();
        $places = Place::orderBy('id')->get();
        // dd("edit", $aid, $activity, $times, $places);
        return view('admin.edit')
                ->with('activity', $activity)
                ->with('times', $times)
                ->with('places', $places);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'act_at' => ['required', 'date'],
            'note' => ['max:140'],
        ]);

        $activity = Activity::where('id', '=', $request->aid)->first();
        if (!$activity) {
            return redirect('/admin/dashboard/')->with('error', "そのような活動予定がありません");
        }

        $activity->act_at = date('Y-m-d', strtotime($request->act_at));
        if ($request->time == "0") {
            $activity->time_id = null;
        } else {
            $activity->time_id = $request->time;
        }
        if ($request->place == "0") {
            $activity->place_id = null;
        } else {
            $activity->place_id = $request->place;
        }
        // $activity->meeting = $request->meeting;
        if ($activity->meeting == '1') {
            if (mb_strlen($request->note) == 0) {
                return back()
                        ->with('error', '【一部の団員対象】を選択した場合は【活動内容】に何か情報を入力してください')
                        ->withInput();
            }
        }
        $activity->note = $request->note;
        $activity->save();

        return redirect('/admin/dashboard/')
            ->with('success', $activity->act_at . "の活動予定を修正しました");
    }
}
