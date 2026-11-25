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

class PlaceControllerTest extends TestCase
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

    public function test_admin_place_controller_with_seed(): void
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
        // dd($admin);
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place')
                        ->assertSee('瀬楽スタジオ')
                        ->assertSee('活動施設一覧')
                        ->assertSee('管理者');
        //
        $url = sprintf("/admin/place/%d", $place_ids[6]->id);
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
                        ->assertSee('瀬楽スタジオ')
                        ->assertSee('活動施設の編集')
                        ->assertSee('デフォルト活動施設')
                        ->assertSee('管理者');
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/99a')
                        ->assertRedirect('/admin/place/');
        //
        //
        //
        //
        //
        // --- testDeleteAsAdmin --------------------------
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/create')
                        ->assertSee('活動施設名')
                        ->assertSee('新規登録')
                        ->assertSee('デフォルト')
                        ->assertSee('管理者');
        //
        // 練習施設の追加
        //
        $response = $this->actingAs($admin, 'admin')
                ->json('POST', '/admin/place', [
                'place' => "新しい練習施設A",
                ]);
        $response = $this->actingAs($admin, 'admin')
                ->json('POST', '/admin/place', [
                'place' => "新しい練習施設B",
                'default_place' => '1',
                ]);
        //
        // 編集できない
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/place', [
                'pid' => '99a',
                'place' => "そんな施設は存在しない",
                ]);
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/place', [
                'pid' => "8",
                'place' => "新しい練習施設B更新",
                ]);
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/place', [
                'pid' => "8",
                'place' => "新しい練習施設A更新デフォルト",
                'default_place' => '1',
                ]);
        // 文化体育館Bは削除できない
        $url = sprintf("/admin/place/%d/delete", $activity_ids[2]->id);
        $redirect_url = sprintf("/admin/place/%d", $activity_ids[2]->id);
        $response = $this->actingAs($admin, 'admin')
                        ->get($url)
                        ->assertRedirect($redirect_url);

        // 施設がない
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/99a/delete')
                        ->assertRedirect('/admin/place');
        //
        //
        //
        // --- testPlaceCreateUpdateDeleteAsAdmin --------------------------
        //
        //
        // B を削除（確認画面）
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/8/delete')
                        ->assertSee('活動施設情報を削除')
                        ->assertSee('この操作は元に戻せません')
                        ->assertSee('登録済み活動回数');

        //
        // B を削除
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/8', []);
        // 削除できない
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/99a', [])
                ->assertRedirect('/admin/dashboard');
        // A を削除し，デフォルトが移行
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/7', [])
                ->assertRedirect('/admin/place');
        //
        //
        //
        //
        //


    }
}
