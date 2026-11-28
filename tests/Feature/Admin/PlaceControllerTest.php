<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Admin;

use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\PlacesTableSeeder;
use Database\Seeders\TimesTableSeeder;
use Database\Seeders\ActivitiesTableSeeder;

class PlaceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_place_controller_with_seed(): void
    {
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);

        $place_ids = DB::table('places')->select('id')->orderBy('id')->get();
        $place_ids->prepend(0);  // 先頭に要素を追加（id と インデックスを同じにしたい）

        $max_place_id = DB::table('places')->max('id');
        $new_place_id1 = $max_place_id + 1;   // 新規追加1回目のID
        $new_place_id2 = $max_place_id + 2;   // 新規追加2回目のID

        $admin = Admin::first();
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place')
                        ->assertSee('瀬楽スタジオ')
                        ->assertSee('活動施設一覧')
                        ->assertSee('管理者');
        //
        $response = $this->actingAs($admin, 'admin')
                        ->get("/admin/place/" . $place_ids[6]->id)
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
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/create')
                        ->assertSee('活動施設名')
                        ->assertSee('新規登録')
                        ->assertSee('デフォルト')
                        ->assertSee('管理者');
        //
        //
        // 文化体育館Bは活動が登録されているので削除できない
        $response = $this->actingAs($admin, 'admin')
                        ->get("/admin/place/" . $place_ids[2]->id . "/delete")
                        ->assertRedirect("/admin/place/" . $place_ids[2]->id);
        //
        //
        // 練習施設の追加
        $response = $this->actingAs($admin, 'admin')
                ->json('POST', '/admin/place', [
                'place' => "新しい練習施設A",
                ]);
        //
        // 練習施設の追加
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
        //
        // $places = DB::table('places')->orderBy('id')->get();
        // dd($places);
        //
        // 編集
        $response = $this->actingAs($admin, 'admin')
                ->json('PATCH', '/admin/place', [
                'pid' => $new_place_id1,
                'place' => "新しい練習施設A更新デフォルト",
                'default_place' => '1',
                ]);
        //
        // 施設がない
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/99a/delete')
                        ->assertRedirect('/admin/place');
        //
        //
        // B を削除（確認画面）
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/place/' . $new_place_id2 . '/delete')
                        ->assertSee('活動施設情報を削除')
                        ->assertSee('この操作は元に戻せません')
                        ->assertSee('登録済み活動回数');

        //
        // B を削除
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/' . $new_place_id2, []);
        //
        // 削除できない
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/99a', [])
                ->assertRedirect('/admin/dashboard');
        //
        // A を削除し，デフォルトが移行
        $response = $this->actingAs($admin, 'admin')
                ->json('DELETE', '/admin/place/' . $new_place_id1, [])
                ->assertRedirect('/admin/place');
        //
        //
    }
}
