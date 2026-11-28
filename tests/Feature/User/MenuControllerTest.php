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
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\MenusTableSeeder;
use Database\Seeders\MailinfosTableSeeder;

class MenuControllerTest extends TestCase
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

    public function test_user_menu_with_seed(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(PartsTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(MenusTableSeeder::class);
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

        // メニューの表示
        $response = $this->actingAs($user)
                        ->get('/menu/1')
                        ->assertSee('練習メニュー')
                        ->assertSee('投稿者名');
        //
        // 見つからない
        $response = $this->actingAs($user)
                        ->get('/menu/999a')
                        ->assertRedirect('/dashboard');
        //
        //
        // メニューの更新（見つからない）
        $response = $this->actingAs($user)
                        ->json('PATCH', '/menu', [
                            'mid' => "9999a",
                            'url' => "/dashboard",
                            'name' => "たろー",
                            'menu' => "あいうえお"
                        ]);
        //
        // メニューの更新
        $response = $this->actingAs($user)
                        ->json('PATCH', '/menu', [
                            'mid' => "1",
                            'url' => "/dashboard",
                            'name' => "たろー",
                            'menu' => "ピアノコンチェルト\n交響曲第9番"
                        ]);
        //
        // メニューの新規登録画面の表示
        $response = $this->actingAs($user)
                        ->get('/menu/create/?aid=2')
                        ->assertSee('練習メニュー')
                        ->assertSee('投稿者名');
        //
        // メニューの新規登録画面の表示（見つからない）
        $response = $this->actingAs($user)
                        ->get('/menu/create/?aid=999a')
                        ->assertRedirect('/dashboard');

        //
        // メニューのメール文面
        $response = $this->actingAs($user)
                        ->get('/menu/2/mail')
                        ->assertSee('案内メール文面')
                        ->assertSee('の練習予定をお知らせします')
                        ->assertSee('じゅげむ');
        //
        // メニューのメール文面（見つからない）
        $response = $this->actingAs($user)
                        ->get('/menu/999a/mail')
                        ->assertRedirect('/dashboard');
        //
        // メニューの「更新」バッジを確認
        $response = $this->actingAs($user)
                        ->get('/dashboard/')
                        ->assertSee('予定')
                        ->assertSee('更新')
                        ->assertSee('A. Sample');
        //
        //
        // メニューの新規登録
        $response = $this->actingAs($user)
                        ->json('POST', '/menu', [
                            'aid' => "8",
                            'url' => "/dashboard",
                            'name' => "たろー",
                            'menu' => "ブラームス"
                        ]);
        //
        // // メニューのメール文面
        // $response = $this->actingAs($user)
        //                 ->get('/menu/1/mail')
        //                 ->assertSee('案内メール文面');
        //                 // ->assertSee('の練習予定をお知らせします')
        //                 // ->assertSee('じゅげむ');
        //
        //
        // $today = Carbon::now()->format('Y-m-d');
        // $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        // $dec12 = Carbon::now()->format('Y') . '-12-12'; // 12月12日

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
        //
        //
        //
        //



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
