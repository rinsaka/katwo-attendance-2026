<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;

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
        dd("edit", $aid);
    }
}
