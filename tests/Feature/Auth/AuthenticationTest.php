<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Database\Seeders\UsersTableSeeder;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'name' => 'A. Sample',
            'email' => 'a@sample.com',
            'login_id' => 'user_a',
        ]);

        $response = $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'name' => 'A. Sample',
            'email' => 'a@sample.com',
            'login_id' => 'user_a',
        ]);

        $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'A. Sample',
            'email' => 'a@sample.com',
            'login_id' => 'user_a',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_users_too_many_login_attempts(): void
    {
        $this->seed(UsersTableSeeder::class);

        $user = User::first();

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response = $this->json('POST', '/login', [
                    'login_id' => $user->login_id,
                    'password' => 'Wrong Password'
        ]);

        $response->assertSee('Too many login attempts');

    }
}
