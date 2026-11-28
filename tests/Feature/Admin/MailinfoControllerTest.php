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

    public function test_admin_mailinfo_with_seed(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);

        $admin = Admin::first();
        $user = User::first();

        //
        $response = $this->actingAs($user)
                        ->get('/dashboard/mail')
                        ->assertRedirect('/dashboard');
        //
        // 次の処理でデータが自動的に投入される
        $response = $this->actingAs($admin, 'admin')
                    ->get('/admin/mailinfo')
                    ->assertSee('フッタ')
                    ->assertSee('編集')
                    ->assertSee('更新')
                    ->assertSee('管理者');
        //
        // もう一度開く
        $response = $this->actingAs($admin, 'admin')
                    ->get('/admin/mailinfo')
                    ->assertSee('メールフッタをここに')
                    ->assertSee('編集')
                    ->assertSee('更新')
                    ->assertSee('管理者');
        //
        // $mailinfos = DB::table('mailinfos')->orderBy('id')->get();
        // dd($mailinfos);
        //
        // 更新成功
        $mailinfo = DB::table('mailinfos')->orderBy('id')->first();
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/mailinfo', [
                            'id' => $mailinfo->id,
                            'key' => $mailinfo->key,
                            'mailinfo' => "ぱぴぷぺぽ",
                        ]);
        $response -> assertRedirect('/admin/mailinfo');
        //
        //
        // 不正なパラメータ
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/mailinfo', [
                            'id' => "99a",
                            'key' => "signiture",
                            'mailinfo' => "ぱぴぷぺぽ",
                        ]);
        $response -> assertRedirect('/admin/dashboard');
    }
}
