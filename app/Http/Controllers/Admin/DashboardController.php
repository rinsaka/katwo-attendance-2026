<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;
use App\Models\Time;
use App\Models\Place;
use App\Models\Attendance;
use App\Models\User;

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

    public function create()
    {
        $times = Time::orderBy('jikan')->get();
        $places = Place::orderBy('place')->get();
        return view('admin.create')
                ->with('times', $times)
                ->with('places', $places);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'act_at' => ['required', 'date'],
            'note' => ['max:140'],
        ]);

        if ($request->meeting == "1") {
            if ($request->note =="") {
                return redirect('/admin/activity/create')->with('error', "「活動形態」を指定した場合は，「内容」に会議名などを指定してください");
            }
        }

        $act_at = date('Y-m-d', strtotime($request->act_at));

        $activity = new Activity();
        $activity->act_at = $act_at;
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
        if ($request->meeting == "1") {
            $activity->meeting = true;
        }
        $activity->note = $request->note;
        $activity->save();

        // すでに同じ月の別の予定に出欠が登録されていれば，その人を「未定」として追加する．
        // 同じ月の活動を取得する
        $thismonth_head = date('Y-m-01',strtotime($act_at));
        $thismonth_tail = date('Y-m-t',strtotime($act_at));

        $acts = Activity::where('act_at', '>=', $thismonth_head)
                                ->where('act_at', '<=', $thismonth_tail)
                                ->where('id', '<>', $activity->id)
                                ->get();
        // dd($acts);
        foreach ($acts as $act) {
            $attendances = Attendance::where('activity_id', '=', $act->id)->get();

            foreach ($attendances as $attendance) {
                // 登録済みかどうか
                $atten_exist = Attendance::where('activity_id', '=', $activity->id)
                                            ->where('name', '=', $attendance->name)
                                            ->first();
                if (!$atten_exist) { // まだ登録されていない
                    $new_atten = new Attendance();
                    $new_atten->activity_id = $activity->id;
                    $new_atten->part_id = $attendance->part_id;
                    $new_atten->name = $attendance->name;
                    $new_atten->attendance = 0;
                    $new_atten->comment = '※活動予定の追加により，この情報が自動的に生成されました．';
                    $new_atten->save();
                }

            }

        }

        return redirect('/admin/dashboard/')
            ->with('success', $activity->act_at . "活動予定を新規登録しました");
    }

    public function destory(Request $request, $id)
    {
        // dd("destory", $request, $id);
        if($request->confirmation != 'yakuin') {
            return redirect('/admin/activity/'.$id)->with('error', "確認用文字列を入力してください");
        }
        $activity = Activity::where('id', '=', $id)->first();
        if (!$activity) {
            return redirect('/admin/dashboard')->with('error', "そのような活動予定がありません");
        }
        $activity->delete();  // cascade にしているので関連した attendances も消える

        return redirect('/admin/dashboard')
                ->with('success', $activity->act_at . " の活動予定を削除しました");

    }

    public function passwd()
    {
        $admin = \Auth::user();
        return view('admin.passwd')
                ->with('id', $admin->id);
    }

    public function passwd_update(Request $request)
    {
        $admin = \Auth::user();
        // 現在のパスワードを確認
        if (!password_verify($request->current_password, $admin->password)) {
            return redirect('/admin/password')
                ->with('error', 'パスワードが違います');
        }
        // ここはありえないはず
        if ($request->id != $admin->id) {
            return redirect('/admin/password')
                ->with('error', 'IDが不正です');
        }

        // Validation（6文字以上あるかなどのチェック）
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ]);


        // Validation（2つが一致しているかのチェック）
        if ($request->new_password != $request->new_password_confirm) {
            return redirect('/admin/password')
                ->with('error', '新しいパスワード（と確認用）が一致しません');
        }

        // パスワードを保存
        $admin->password = bcrypt($request->new_password);
        $admin->save();
        return redirect('/admin/dashboard')
                ->with('success', 'パスワードを変更しました');
    }

    /*
     *   ユーザ（団員）パスワードの変更画面
     */
    public function userpasswd()
    {
        $admin = \Auth::user();
        return view('admin.userpasswd')
                ->with('id', $admin->id);
    }

    public function userpasswd_update(Request $request)
    {
        $admin = \Auth::user();
        // 管理者パスワードを確認
        if (!password_verify($request->password, $admin->password)) {
            return redirect('/admin/userpassword')
                ->with('error', '管理者パスワードが違います');
        }
        // Validation（6文字以上あるかなどのチェック）
        $validated = $request->validate([
            'login_id' => ['required'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        // Validation（2つが一致しているかのチェック）
        if ($request->new_password != $request->new_password_confirm) {
            return redirect('/admin/userpassword')
                ->with('error', '新しいパスワード（と確認用）が一致しません');
        }

        $user = User::where('login_id', '=', $request->login_id)->first();
        if (!$user) {
            return redirect('/admin/userpassword')
                ->with('error', '団員のログインIDが違います');
        }
        // パスワードを保存
        $user->password = bcrypt($request->new_password);
        $user->save();
        return redirect('/admin/dashboard')
                ->with('success', '団員 '. $user->login_id . ' のパスワードを変更しました');
    }

    /*
     *
     * 練習場所の一覧
     *
     */
    public function place()
    {
        $places = Place::orderBy('place')->get();
        return view('admin.place.index')
                ->with('places', $places);
    }

    public function place_edit($pid)
    {
        dd($pid);
    }

}
