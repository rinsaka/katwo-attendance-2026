<?php

namespace Tests\Feature\Admin;

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


    public function testLoginAsAdmin()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);
        $this->seed(PlacesTableSeeder::class);
        $this->seed(TimesTableSeeder::class);
        $this->seed(PartsTableSeeder::class);
        $this->seed(ActivitiesTableSeeder::class);

        $admin = Admin::where('login_id', 'taro')->first();
        // dd($admin);
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/dashboard/')
                        ->assertSee('土')
                        ->assertSee('体育館')
                        ->assertSee('管理者');

        // $response = $this->actingAs($admin, 'admin')
        //                   ->get('/admin/activity/16')
        //                   ->assertSee('日にち')
        //                   ->assertSee('時間')
        //                   ->assertSee('場所')
        //                   ->assertSee('管理者');

        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/activity/99');

    }

}
