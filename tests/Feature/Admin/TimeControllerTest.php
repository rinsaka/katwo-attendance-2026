<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Admin;
use App\Models\Time;
// use App\Models\Place;
// use App\Models\Part;
// use App\Models\Activity;

use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\PlacesTableSeeder;
use Database\Seeders\TimesTableSeeder;
use Database\Seeders\PartsTableSeeder;
use Database\Seeders\ActivitiesTableSeeder;

class TimeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_time_with_seed(): void
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

        $max_time_id = DB::table('times')->max('id');   //.   -> 4
        $new_time_id1 = $max_time_id + 1;   // 新規追加1回目のID -> 5
        $new_time_id2 = $max_time_id + 2;   // 新規追加2回目のID -> 6
        // dd($max_time_id, $new_time_id1, $new_time_id2);


        //
        // --- testTimeAsAdmin -------------------------------
        //
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/time')
                        ->assertSee('13:00 - 17:00')
                        ->assertSee('活動時間一覧')
                        ->assertSee('デフォルト')
                        ->assertSee('管理者');
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get("/admin/time/" . $time_ids[1]->id)
                        ->assertSee('13:00 - 17:00')
                        ->assertSee('活動時間の編集')
                        ->assertSee('デフォルト活動時間')
                        ->assertSee('管理者');
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/time/99a')
                        ->assertRedirect('/admin/time/');
        //
        //
        // --- testTimeCreateUpdateDeleteAsAdmin -------------------------------
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/time/create')
                        ->assertSee('活動時間')
                        ->assertSee('新規登録')
                        ->assertSee('デフォルト')
                        ->assertSee('管理者');
        //
        // 活動時間の追加
        $response = $this->actingAs($admin, 'admin')
                ->json('POST', '/admin/time', [
                'jikan' => "新しい練習時間A",
                ]);
        $response = $this->actingAs($admin, 'admin')
                ->json('POST', '/admin/time', [
                'jikan' => "新しい練習時間B",
                'default_jikan' => '1',
                ]);
        //
        // 編集できない
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => '99a',
                'jikan' => "そんな時間は存在しない",
                ]);
        //
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => $new_time_id2,
                'jikan' => "新しい練習時間B更新",
                ]);
        //
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => $new_time_id1,
                'jikan' => "新しい練習時間A更新デフォルト",
                'default_jikan' => '1',
                ]);
        // 時間18:00-22:00は削除できない
        $response = $this->actingAs($admin, 'admin')
                        ->get("/admin/time/" . $time_ids[3]->id . "/delete")
                        ->assertRedirect("/admin/time/" . $time_ids[3]->id);
        //
        // 時間がない
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/time/99a/delete')
                        ->assertRedirect('/admin/time');
        //
        // B を削除（確認画面）
        $response = $this->actingAs($admin, 'admin')
                        ->get("/admin/time/" . $new_time_id2 . "/delete")
                        ->assertSee('活動時間情報を削除')
                        ->assertSee('この操作は元に戻せません')
                        ->assertSee('登録済み活動回数');
        //
        // B を削除
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', "/admin/time/" . $time_ids[3]->id, []);
        //
        // 削除できない
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/time/99a', [])
                ->assertRedirect('/admin/time');
        //
        // A を削除し，デフォルトが移行
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', "/admin/time/" . $new_time_id1 , [])
                ->assertRedirect('/admin/time');
        //
    }
}
