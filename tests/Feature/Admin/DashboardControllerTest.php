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
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_with_seed(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(PartsTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);

        $admin = Admin::where('login_id', 'taro')->first();
        // dd($admin);
        $response = $this->actingAs($admin, 'admin')  // ガードを変更
                            ->get('/admin/dashboard/')
                            ->assertSee('活動予定一覧')
                            ->assertSee('管理者モード')
                            ->assertSee($admin->name);
        //
    }


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



        $admin = Admin::where('login_id', 'taro')->first();
        $user = User::where('login_id', 'user_a')->first();

        // dd($admin);
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/dashboard/')
                        ->assertSee('土')
                        ->assertSee('体育館')
                        ->assertSee('管理者');
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/activity/99');

        // --- testEditAsAdmin --------------------------

        $acts = $this->mk_activities();
        list($year, $month, $day) = explode('-', $acts[15]);
        $day = sprintf("%d",(int)$day + 1);
        $new_day = "$year-$month-$day"; // 1日進める

        // dd($time_ids, $place_ids);

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

}
