<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Admin;
use Database\Seeders\AdminsTableSeeder;


class LoginControllerTest extends TestCase
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

    public function test_admin_login_with_seed(): void
    {
        $this->seed(AdminsTableSeeder::class);

        $admin = Admin::where('login_id', 'taro')->first();

        $this->get('/admin/login/')
                            ->assertSee('Admin Login')
                            ->assertSee('Password');
        //
        // ログイン成功
        $response = $this->json('POST', '/admin/login', [
                    'login_id' => $admin->login_id,
                    'password' => 'admin'
                ]);
        $response->assertRedirect('/admin/dashboard');
        //
        // パスワードが異なる -> ステータスコード 422 になる
        $response = $this->json('POST', '/admin/login', [
                    'login_id' => $admin->login_id,
                    'password' => 'Wrong Password'
                ]);
        $response->assertStatus(422);
        //
        //
        $response = $this->actingAs($admin, 'admin')  // ガードを変更
            ->json('POST', '/admin/logout', []);
        $response->assertRedirect('/');
        //
        //
    }
}
