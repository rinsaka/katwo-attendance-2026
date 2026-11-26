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
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

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
        $url = sprintf("/admin/time/%d", $time_ids[1]->id);
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
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
        // 編集できない
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => '99a',
                'jikan' => "そんな時間は存在しない",
                ]);
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => '6',
                'jikan' => "新しい練習時間B更新",
                ]);
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/time', [
                'tid' => '5',
                'jikan' => "新しい練習時間A更新デフォルト",
                'default_jikan' => '1',
                ]);
        // 時間18:00-22:00は削除できない
        $url = sprintf("/admin/time/%d/delete", $time_ids[3]->id);
        $url_redirect = sprintf("/admin/time/%d", $time_ids[3]->id);
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
                        ->assertRedirect($url_redirect);

        // 時間がない
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/time/99a/delete')
                        ->assertRedirect('/admin/time');
        // B を削除（確認画面）
        $url = "/admin/time/6/delete";
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
                        ->assertSee('活動時間情報を削除')
                        ->assertSee('この操作は元に戻せません')
                        ->assertSee('登録済み活動回数');

        // B を削除
        $url = sprintf("/admin/time/%d", $time_ids[3]->id);
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', $url, []);
        // 削除できない
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/time/99a', [])
                ->assertRedirect('/admin/time');
        // A を削除し，デフォルトが移行
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', "/admin/time/5", [])
                ->assertRedirect('/admin/time');
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
}
