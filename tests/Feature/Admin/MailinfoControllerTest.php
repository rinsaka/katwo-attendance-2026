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

class MailinfoControllerTest extends TestCase
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

    public function test_admin_mailinfo_with_seed(): void
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

        // // 日付の取得
        // $year = (int)date('Y');
        // $month = (int)date('m');
        // // var_dump($this->year);

        // // 3ヶ月前から8ヶ月後までの年と月を配列に格納
        // $this->ymlist = array();
        // for ($i = -3; $i <= 8; $i++) {
        //     $this->ymlist[$i] = $this->shift_month($year, $month, $i);
        // }

        $admin = Admin::where('login_id', 'taro')->first();
        $user = User::where('login_id', 'user_a')->first();

        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/mail')
                        ->assertRedirect('/dashboard');

        // 次の処理でデータが自動的に投入される
        $response = $this->actingAs($admin, 'admin')
                    ->get('/admin/mailinfo')
                    ->assertSee('フッタ')
                    ->assertSee('編集')
                    ->assertSee('更新')
                    ->assertSee('管理者');
        //
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/mailinfo', [
                            'id' => "1",
                            'key' => "signiture",
                            'mailinfo' => "ぱぴぷぺぽ",
                        ]);
        //
        // 不正なパラメータ
        $response = $this->actingAs($admin, 'admin')
                        ->json('POST', '/admin/mailinfo', [
                            'id' => "99a",
                            'key' => "signiture",
                            'mailinfo' => "ぱぴぷぺぽ",
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
}
