<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Admin;

use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AdminsTableSeeder;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_password_with_seed(): void
    {
        /*
         *    管理者とユーザについてパスワードを変更する
         */

        $this->seed(UsersTableSeeder::class);
        $this->seed(AdminsTableSeeder::class);


        $admin = Admin::first();
        $user = User::first();

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
                            'login_id' => $user->login_id,
                            'new_password' => "12345678",
                            'new_password_confirm' => "12345678",
                        ]);
        //
        // 団員パスワード変更：パスワードが一致しない
        $response = $this->actingAs($admin, 'admin')
                        ->json('PATCH', '/admin/userpassword', [
                            'id' => $user->id,
                            'password' => env('ADMIN_PASSWORD'),
                            'login_id' => $user->login_id,
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
                            'login_id' => $user->login_id,
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
    }
}
