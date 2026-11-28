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
// use Database\Seeders\PlacesTableSeeder;
// use Database\Seeders\TimesTableSeeder;
// use Database\Seeders\PartsTableSeeder;
// use Database\Seeders\ActivitiesTableSeeder;

class PasswordControllerTest extends TestCase
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

    public function test_admin_password_with_seed(): void
    {
        /*
        * 次のユーザについてパスワードを変更する
        *   user -> user_b
        *   admin -> jiro
        */

        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);

        $admin = Admin::where('login_id', 'jiro')->first();
        $user = User::where('login_id', 'user_b')->first();

        // 団員パスワード変更ページ
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/userpassword')
                        ->assertSee('団員パスワードの変更')
                        ->assertSee('ログインID')
                        ->assertSee('変更されません')
                        ->assertSee('新しいパスワード');
        //
        // 団員パスワード変更：管理者パスワードが違う
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/userpassword', [
                            'id' => $user->id,
                            'password' => "abc",
                            'login_id' => "user_b",
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        // 団員パスワード変更：パスワードが一致しない
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/userpassword', [
                            'id' => $user->id,
                            'password' => env('ADMIN_PASSWORD'),
                            'login_id' => "user_b",
                            'new_password' => "1234567",
                            'new_password_confirm' => "1234567890",
                        ]);
        //
        // 団員パスワード変更：団員のログインIDが異なる
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/userpassword', [
                            'id' => $user->id,
                            'password' => env('ADMIN_PASSWORD'),
                            'login_id' => "acb",
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        // 団員パスワード変更：成功
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/userpassword', [
                            'id' => $user->id,
                            'password' => env('ADMIN_PASSWORD'),
                            'login_id' => "user_b",
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        //
        /////////////////////////////
        //
        // 管理者パスワード変更ページ
        $response = $this->actingAs($admin, 'admin')
                        ->get('/admin/password')
                        ->assertSee('管理者パスワードの変更')
                        ->assertSee('現在のパスワード')
                        ->assertSee('新しいパスワード');

        //
        // 管理者パスワード変更：パスワードが違う
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/password', [
                            'id' => $admin->id,
                            'current_password' => "abc",
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        // 管理者パスワード変更：IDが不正（本来あり得ない）
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/password', [
                            'id' => "999a",
                            'current_password' => env('ADMIN_PASSWORD'),
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        // 管理者パスワード変更：パスワードが短い
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/password', [
                            'id' => $admin->id,
                            'current_password' => env('ADMIN_PASSWORD'),
                            'new_password' => "123",
                            'new_password_confirm' => "123",
                        ]);
        //
        // 管理者パスワード変更：パスワードが一致しない
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/password', [
                            'id' => $admin->id,
                            'current_password' => env('ADMIN_PASSWORD'),
                            'new_password' => "1234567",
                            'new_password_confirm' => "123456789",
                        ]);
        //
        // 管理者パスワード変更：成功
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/password', [
                            'id' => $admin->id,
                            'current_password' => env('ADMIN_PASSWORD'),
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
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
