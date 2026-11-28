<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Admin;
// use App\Models\Time;
// use App\Models\Place;
// use App\Models\Part;
// use App\Models\Activity;

use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\PlacesTableSeeder;
use Database\Seeders\TimesTableSeeder;
use Database\Seeders\PartsTableSeeder;
use Database\Seeders\ActivitiesTableSeeder;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDashboardContollerAsAdmin()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(PartsTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);

        $time_ids = DB::table('times')->select('id')->orderBy('id')->get();
        $time_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $place_ids = DB::table('places')->select('id')->orderBy('id')->get();
        $place_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $activity_ids = DB::table('activities')->select('id')->orderBy('id')->get();
        $activity_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $part_ids = DB::table('parts')->select('id')->orderBy('id')->get();
        $part_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        // 日付の取得
        $year = (int)date('Y');
        $month = (int)date('m');
        // var_dump($this->year);

        // 3ヶ月前から8ヶ月後までの年と月を配列に格納
        $this->ymlist = array();
        for ($i = -3; $i <= 8; $i++) {
            $this->ymlist[$i] = $this->shift_month($year, $month, $i);
        }

        $admin = Admin::first();
        $user = User::first();

        // dd($admin);
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/dashboard/')
                        ->assertSee('土')
                        ->assertSee('体育館')
                        ->assertSee('管理者');
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/activity/99a')
                        ->assertRedirect('/admin/dashboard');

        // --- testEditAsAdmin --------------------------

        $acts = $this->mk_activities();
        list($year, $month, $day) = explode('-', $acts[15]);
        $day = sprintf("%d",(int)$day + 1);
        $new_day = "$year-$month-$day"; // 1日進める

        // dd($time_ids, $place_ids, $acts);

        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/activity', [
                            'act_at' => $new_day,
                            'time' => $time_ids[1],
                            'place' => $place_ids[1],
                            'note' => "土曜日から日曜日に変更しました",
                        ]);
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/activity', [
                            'aid' => $activity_ids[16],
                            'act_at' => $new_day,
                            'time' => $time_ids[0],
                            'place' => $place_ids[0],
                            'note' => "時間と場所を一旦削除します",
                        ]);
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/activity', [
                            'aid' => $activity_ids[16],
                            'act_at' => $new_day,
                            'time' => $time_ids[1],
                            'place' => $place_ids[1],
                            'note' => "土曜日から日曜日に変更しました",
                        ]);

        //
        //
        $url = sprintf("/admin/activity/%d", $activity_ids[1]->id);
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
                        ->assertSee('活動予定の表示と編集')
                        ->assertSee('日にち')
                        ->assertSee('活動予定を変更')
                        ->assertSee('管理者');

        //
        //
        // --- testEditAsAdmin --------------------------
        //
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/activity/create')
                        ->assertSee('日にち')
                        ->assertSee('時間')
                        ->assertSee('場所')
                        ->assertSee('登録');
        //
        //
        // 4ヶ月先の4回目の練習を追加する
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[16],
                            'time' => $time_ids[4],
                            'place' => $place_ids[6],
                            'note' => "練習日程を追加しました"
                        ]);

        //
        //
        // 7ヶ月先の予定(2回)を順次追加 --> まず1回め
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[18],
                            'time' => $time_ids[0],
                            'place' => $place_ids[0],
                            'note' => "練習日程を追加しました"
                        ]);
        //
        //
        // 活動形態の指定でエラー
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[18],
                            'time' => "",
                            'place' => $place_ids[1],
                            'meeting' => "1",
                            'note' => ""
                        ]);
        //
        //
        //
        // --- testEditAsUser --------------------------
        //
        //
        // ユーザで出席登録
        // list($year,$month,$day) = explode('-', $acts[18]);
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $year,
                            'month' => $month,
                            'n_act' => "1",
                            'part' => $part_ids[9],
                            'name' => "天然記念物",
                            'act19' => "3",
                            'comment19' => "参加します．このあと，スケジュールが追加されます"
                        ]);
        //
        //
        // --- testCreateAsAdmin2 --------------------------
        //
        // 7ヶ月先の予定(2回目)を追加
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[19],
                            'time' => $time_ids[1],
                            'place' => $place_ids[1],
                            'note' => "さらに追加された日程です"
                        ]);
        //
        // 7ヶ月先の予定(3回目：選曲委員会)をさらに追加
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[19],
                            'time' => null,
                            'place' => $place_ids[2],
                            'note' => "選曲委員会 10:00~12:00"
                        ]);
        //
        //
        //
        // --- testDeleteAsAdmin --------------------------

        $response = $this->actingAs($admin, 'admin')
                        ->json('DELETE', '/admin/activity/19', [     # ここは id を直打ち
                            'confirmation' => "hoge",
                        ]);
        $response = $this->actingAs($admin, 'admin')
                        ->json('DELETE', '/admin/activity/99', [
                            'confirmation' => "yakuin",
                        ]);

        $response = $this->actingAs($admin, 'admin')
                        ->json('DELETE', '/admin/activity/19', [     # ここは id を直打ち
                            'confirmation' => "yakuin",
                        ]);
        //
        //
        //
        //
        // --- testDeleteAsAdmin --------------------------
        //
        $acts = $this->mk_activities();
        // dd($acts[0]);
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/activity', [
                            'act_at' => $acts[0],
                            'time' => null,
                            'place' => $place_ids[1],
                            'meeting' => "1",
                            'note' => "選曲委員会 10:00~12:00"
                        ]);
        //
        // 一部団員対象：活動内容を空で更新エラー
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/activity', [
                            'aid' => "22",
                            'act_at' => $acts[0],
                            'time' => null,
                            'place' => $place_ids[1],
                            'meeting' => "1",
                            'note' => ""
                        ]);
        // 参加登録
        // dd($user, $this->ymlist, $this->ymlist[0][0], $this->ymlist[0][1]);
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => "1",
                            'name' => "あい うえお",
                            'act22' => "3",
                            'comment22' => "あいうえおあいうえおあいうえおあいうえお",
                            'act1' => "1",
                            'comment1' => "おあいうえおおあいうえおおあ",
                            'act2' => "3",
                            'comment2' => "",
                            'act3' => "0",
                            'comment3' => "これもコメント JSON"
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => "3",
                            'name' => "かき くけこ",
                            'act22' => "1",
                            'comment22' => "",
                            'act1' => "0",
                            'comment1' => "",
                            'act2' => "0",
                            'comment2' => "",
                            'act3' => "0",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[2],
                            'name' => "さし すせそ",
                            'act22' => "-1",
                            'comment22' => "",
                            'act1' => "0",
                            'comment1' => "",
                            'act2' => "1",
                            'comment2' => "",
                            'act3' => "1",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[2],
                            'name' => "たち つてと",
                            'act22' => "-1",
                            'comment22' => "",
                            'act1' => "0",
                            'comment1' => "",
                            'act2' => "0",
                            'comment2' => "",
                            'act3' => "0",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[3],
                            'name' => "なにぬ ねの",
                            'act22' => "1",
                            'comment22' => "",
                            'act1' => "1",
                            'comment1' => "",
                            'act2' => "1",
                            'comment2' => "",
                            'act3' => "1",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[4],
                            'name' => "はひ ふへほ",
                            'act22' => "3",
                            'comment22' => "はひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへお",
                            'act1' => "3",
                            'comment1' => "はひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへお",
                            'act2' => "3",
                            'comment2' => "はひふへお",
                            'act3' => "3",
                            'comment3' => "はひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへおはひふへお"
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[5],
                            'name' => "まみ むめも",
                            'act22' => "-1",
                            'comment22' => "",
                            'act1' => "3",
                            'comment1' => "",
                            'act2' => "3",
                            'comment2' => "",
                            'act3' => "3",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[7],
                            'name' => "やいゆ えよ",
                            'act22' => "3",
                            'comment22' => "",
                            'act1' => "3",
                            'comment1' => "",
                            'act2' => "3",
                            'comment2' => "",
                            'act3' => "3",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'part' => $part_ids[8],
                            'name' => "わおん",
                            'act22' => "3",
                            'comment22' => "",
                            'act1' => "1",
                            'comment1' => "",
                            'act2' => "3",
                            'comment2' => "",
                            'act3' => "0",
                            'comment3' => ""
                        ]);
        //
        $response = $this->actingAs($user)
                        ->json('PATCH', '/dashboard', [
                            'year' => $this->ymlist[0][0],
                            'month' => $this->ymlist[0][1],
                            'n_act' => "4",
                            'aid' => "15",
                            'part' => $part_ids[1],
                            'name' => "あい うえお",
                            'atten15' => "-1",
                            'comment15' => "メンバー外でしたあいうえおあいうえおあいうえおあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえお",
                            'atten12' => "0",
                            'comment12' => "未定に変更おあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえおおあいうえお あい",
                            'atten13' => "3",
                            'comment13' => "",
                            'atten14' => "0",
                            'comment14' => "これもコメント JSON add"
                        ]);
        //

        //
        //
        //
        //
        //
        //
        //
        //
        //
        //


    }

    /**
     *    活動の一覧を作成する
    *       今月 3回
    *       翌月 4回（4回目運営会議）
    *       翌々月 3回（3回目総会）
    *       3ヶ月先 3回
    *       4ヶ月先 4回    （テストで1つ追加するのでシーダの内容と異なる）
    *       6ヶ月先 1回（本番）
    **/
    private function mk_activities()
    {
        $act_list = array(     // 月ごとの練習回数と曜日を指定
            ['count'=>3, 'w'=>6],
            ['count'=>4, 'w'=>6],
            ['count'=>3, 'w'=>6],
            ['count'=>3, 'w'=>6],
            ['count'=>4, 'w'=>6],
            ['count'=>0, 'w'=>6],
            ['count'=>0, 'w'=>6],
            ['count'=>3, 'w'=>6],
            ['count'=>1, 'w'=>0],
        );
        $year = date('Y');
        $month = date('m');
        $activities = array();
        $iter = 0;
        foreach($act_list as $act) {
            $m = $month + $iter;
            if ($m == 13) {
                $year = $year + 1;
                $m = $m -12;
            } elseif ($m > 13) {
                $m = $m - 12;
            }
            $tmp_acts = $this->get_activities($year, $m, $act['count'], $act['w']);
            if ($tmp_acts) {
                foreach($tmp_acts as $tmp_act) {
                    $activities[] = $tmp_act;
                }
            }

            $iter++;
        }
        return $activities;
    }

    /**
     *    年と月を指定し，$count 回数だけ $w 曜日の日にちを返す．
    *
    *       $w : 0=>日，1=>月, ... 6=>土
    **/
    private function get_activities($year, $month, $count = 1, $w = 6)
    {
        if($count == 0) {
            return false;
        }
        $d = new \DateTime();
        $d->setDate($year, $month, 1);
        $t = $d->format('t'); // 月末の日にちを取得
        $zm = sprintf('%02d', $month);

        $n_activities = 0;
        $activities = array();
        for ($i = 1; $i <= $t; $i++) {
            $zi = sprintf('%02d', $i);
            $date = date('w', strtotime($year.$zm.$zi));
            if ($date == $w) {
                $activities[] = $year.'-'.$zm.'-'.$zi;
                $n_activities++;
                if ($n_activities == $count) {
                    break;
                }
            }
        }
        return $activities;
    }


    /**
     *     月をずらす
    *
    *
    **/
    private function shift_month($year, $month, $shift = 0)
    {
        if ($shift == 0) {
            return array($year, $month);
        } elseif ($shift > 0) {  // 増やす
            for ($i = 1; $i <= $shift; $i++) {
                if ($month == 12) {
                    $year++;
                    $month = 1;
                } else {
                    $month++;
                }
            }
            return array($year, $month);
        } else { // 減らす
            for ($i = -1; $i >= $shift; $i--) {
                if ($month == 1) {
                    $year--;
                    $month = 12;
                } else {
                    $month--;
                }
            }
            return array($year, $month);
        }
    }

}
