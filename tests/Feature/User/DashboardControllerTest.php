<?php

namespace Tests\Feature\User;

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
use Database\Seeders\MenusTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\MailinfosTableSeeder;

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

    public function test_user_dashboard_with_seed(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(PartsTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);
        $this->seed(MenusTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(MailinfosTableSeeder::class);

        $time_ids = DB::table('times')->select('id')->orderBy('id')->get();
        $time_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $place_ids = DB::table('places')->select('id')->orderBy('id')->get();
        $place_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $activity_ids = DB::table('activities')->select('id')->orderBy('id')->get();
        $activity_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $part_ids = DB::table('parts')->select('id')->orderBy('id')->get();
        $part_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $attendance_ids = DB::table('attendances')->select('id')->orderBy('id')->get();
        $attendance_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        // 日付の取得
        $year = (int)date('Y');
        $month = (int)date('m');
        // var_dump($this->year);

        // 3ヶ月前から8ヶ月後までの年と月を配列に格納
        $this->ymlist = array();
        for ($i = -3; $i <= 8; $i++) {
            $this->ymlist[$i] = $this->shift_month($year, $month, $i);
        }



        $admin = Admin::where('login_id', 'taro')->first();
        $user = User::where('login_id', 'user_a')->first();

        // エラーになるので，それを確認する．
        $response = $this->get('/hoge');
        $response->assertStatus(404);

        //
        $response = $this->actingAs($user)
                        ->get('/dashboard')
                        ->assertSee('予定')
                        ->assertSee('テーマ')
                        ->assertSee('A. Sample');
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[2][0] . '/' . $this->ymlist[2][1])
                        ->assertSee('登録')
                        ->assertSee('文化');
        //
        // dd('/dashboard/'. $this->ymlist[4][0] . '/' . $this->ymlist[4][1]);
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[4][0] . '/' . $this->ymlist[4][1])
                        ->assertSee('登録')
                        ->assertSee('文化体育館');
        //
        $response = $this->actingAs($user)
                        // ->get('/dashboard/2019/01')
                        ->get('/dashboard/'. $this->ymlist[5][0] . '/' . $this->ymlist[5][1])
                        ->assertSee('登録')
                        ->assertSee('まだ');
        //


        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[1][0] . '/' . $this->ymlist[1][1] .'/create')
                        ->assertSee('パート')
                        ->assertSee('必須')
                        ->assertSee('ニックネーム')
                        ->assertSee('メッセージ')
                        ->assertSee('登録')
                        ->assertSee('戻る');
        //

        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1] .'/create')
                        ->assertRedirect('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1]);
        //
        // 1月，12月の切り替えチェック
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[0][0] . '/1')
                        ->assertSee('12月')
                        ->assertSee('2月');

        // 1月，12月の切り替えチェック
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[0][0] . '/12')
                        ->assertSee('11月')
                        ->assertSee('1月');

        // 不正なURL
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[0][0] . '/13')
                        ->assertRedirect('/dashboard/');

        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/mail')
                        ->assertSee('メールのフッタ')
                        ->assertSee('戻る');
        // //
        //
        //
        // --- testCreateAsUser-----------------
        //
        // 登録成功
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "8",
                            'name' => "test_json",
                            'act4' => "3",
                            'comment4' => "JSON テストコメント",
                            'act5' => "3",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "これもコメント JSON",
                            'act7' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertRedirect('/dashboard/'. $this->ymlist[1][0] . '/'. $this->ymlist[1][1]);
        //
        // 名前が重複
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "8",
                            'name' => "たろー",  // 名前が重複
                            'act4' => "3",
                            'comment4' => "JSON テストコメント",
                            'act5' => "2",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "これもコメント JSON",
                            'act7' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertRedirect('/dashboard/'. $this->ymlist[1][0] . '/'. $this->ymlist[1][1] . '/create'); // 名前が重複
        //
        // パート未選択
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "",  // パート未選択
                            'name' => "ながいなまえ",
                            'act4' => "3",
                            'comment4' => "JSON テストコメント",
                            'act5' => "3",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "これもコメント JSON",
                            'act7' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertStatus(422); // パート未選択
        //
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "2",
                            'name' => "ながいなまえながいなまえながいなまえながいなまえながいなまえながいなまえながいなまえながいなまえながいなまえながいなまえ",  // 名前長い
                            'act4' => "3",
                            'comment4' => "JSON テストコメント",
                            'act5' => "3",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "これもコメント JSON",
                            'act7' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertStatus(422); // 名前が長い
        //
        //
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "2",
                            'name' => "長いコメント",
                            'act4' => "3",
                            'comment4' => "コメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよコメントは最大140文字ですよ",
                            'act5' => "1",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "これもコメント JSON",
                            'act7' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertStatus(422); // コメントが長い
        //
        //
        // --- testShowNewAsUser -----------------
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[1][0] . '/' . $this->ymlist[1][1])
                        ->assertSee('新規')
                        // ->assertSee('更新')
                        ->assertSee('A. Sample');
        //
        //
        // --- testEditAsUser -----------------
        //
        // dd('/dashboard/'. $this->ymlist[2][0] . '/' . $this->ymlist[2][1] . '/' . $attendance_ids[2]->id . '/edit');
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[2][0] . '/' . $this->ymlist[2][1] . '/' . $attendance_ids[2]->id . '/edit')
                        ->assertSee('パート')
                        ->assertSee('予定を変更します')
                        ->assertSee('コメント')
                        ->assertSee('予定を変更')
                        ->assertSee('戻る');

        // dd('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1] . '/12/edit');
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1] . '/' . $attendance_ids[2]->id . '/edit')
                        ->assertRedirect('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1]);
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1] . '/9999/edit')
                        ->assertRedirect('/dashboard/'. $this->ymlist[-3][0] . '/' . $this->ymlist[-3][1]);
        //
        $response = $this->actingAs($user)
                        // ->get('/dashboard/2018/08/99/edit')
                        ->get('/dashboard/'. $this->ymlist[0][0] . '/' . $this->ymlist[0][1] . '/99/edit')
                        ->assertRedirect('/dashboard/'. $this->ymlist[0][0] . '/' . $this->ymlist[0][1]);
        //
        //
        //
        // --- testUpdateAsUser -----------------
        //
        //
        $response = $this->actingAs($user)
                        ->json('PATCH', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'aid' => $activity_ids[12]->id,
                            'part' => $part_ids[8]->id,
                            'name' => "test_json",
                            'atten9' => "3",
                            'comment4' => "JSON テストコメント 更新",
                            'atten10' => "0",
                            'comment5' => "",
                            'atten11' => "1",
                            'comment6' => "これもコメント JSON",
                            'atten12' => "3",
                            'comment7' => "JSON JSON"
                        ]);
        $response->assertRedirect('/dashboard/' . $this->ymlist[1][0] . '/' . $this->ymlist[1][1]);
        //
        //
        //
        // --- testShowUpdateAsUser -----------------
        //
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/'. $this->ymlist[1][0] . '/' . $this->ymlist[1][1])
                        // ->assertSee('新規')
                        ->assertSee('更新')
                        ->assertSee('A. Sample');
        //
        //
        //
        //
        // --- testCreateAndDelete -----------------
        //
        // 追加して削除する
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'n_act' => "4",
                            'part' => "1",
                            'name' => "サブロー",
                            'act4' => "3",
                            'comment4' => "あとで削除します",
                            'act5' => "3",
                            'comment5' => "",
                            'act6' => "1",
                            'comment6' => "削除してね",
                            'act7' => "3",
                            'comment7' => "これ失敗"
                        ]);
        $response->assertRedirect('/dashboard/' . $this->ymlist[1][0] . '/' . $this->ymlist[1][1]);
        //
        // 上で登録した4つの出席データのIDを取得
        $attens = DB::table('attendances')->orderBy('id', 'DESC')->limit(4)->get();
        $delete_ids = [];
        foreach($attens as $atten) {
            $delete_ids[] = $atten->id;
        }
        //
        // 削除確認画面
        $response = $this->actingAs($user)
                        ->json('POST', '/dashboard/confirm_delete', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'name' => "サブロー",
                            'aid' => $delete_ids[3],
                            'attens' => $delete_ids
                        ]);
        $response->assertSee('サブロー')
                    ->assertSee('確認用');
        //
        // 削除エラー
        $response = $this->actingAs($user)
                        ->json('DELETE', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'name' => "サブロー",
                            'aid' => $delete_ids[3],
                            'attens' => $delete_ids,
                            'delete_token' => "katwo",
                            'confirmation' => "hoge"  // 文字列が異なるので削除エラー
                        ]);
        $response->assertRedirect('/dashboard/' . $this->ymlist[1][0] . '/' . $this->ymlist[1][1] . '/' . $delete_ids[3] . '/edit');
        //
        // 削除成功
        $response = $this->actingAs($user)
                        ->json('DELETE', '/dashboard', [
                            'year' => $this->ymlist[1][0],
                            'month' => $this->ymlist[1][1],
                            'name' => "サブロー",
                            'aid' => $delete_ids[3],
                            'attens' => $delete_ids,
                            'delete_token' => "katwo",
                            'confirmation' => "katwo"
                        ]);
        $response->assertRedirect('/dashboard/' . $this->ymlist[1][0] . '/' . $this->ymlist[1][1]);
        //
        //
        //
        //
        // --- testListAllActAsUser -----------------
        //
        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/list')
                        ->assertSee('すべての予定')
                        ->assertSee('A. Sample');
        //
        //
        //
        //

        /*
        |--------------------------------------------------------------------------
        | 旧URL対応
        |    /home を /dashborad に変更したので転送させる
        |--------------------------------------------------------------------------
        */

        $urls = [
            '/home',
            '/home/2026',
            '/home/2026/10',
            '/home/2026/10/6/edit',
        ];

        foreach ($urls as $url) {
            $this->actingAs($user)
                ->get($url)
                ->assertRedirect(route('dashboard'));
        }


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
