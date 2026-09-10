<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_json(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'กานดา วงศ์ษา',
            'email' => 'kanda@example.com',
            'phone' => '089-987-6543',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ',
            'user' => [
                'name' => 'กานดา วงศ์ษา',
                'email' => 'kanda@example.com',
                'phone' => '089-987-6543',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'กานดา วงศ์ษา',
            'email' => 'kanda@example.com',
            'phone' => '089-987-6543',
        ]);

        $this->assertAuthenticated();
    }

    public function test_registration_validation_fails_when_email_exists(): void
    {
        User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'phone' => '081-234-5678',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/register', [
            'name' => 'สมชาย คนใหม่',
            'email' => 'somchai@example.com',
            'phone' => '082-222-3333',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_validation_fails_when_passwords_do_not_match(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'เอกชัย ใจดี',
            'email' => 'ekkachai@example.com',
            'phone' => '085-555-5555',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'phone' => '081-234-5678',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/login', [
            'login' => 'somchai@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'เข้าสู่ระบบสำเร็จ',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_phone(): void
    {
        $user = User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'phone' => '081-234-5678',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/login', [
            'login' => '081-234-5678',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'เข้าสู่ระบบสำเร็จ',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'phone' => '081-234-5678',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/login', [
            'login' => 'somchai@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertGuest();
    }

    public function test_auth_user_endpoint_returns_current_user(): void
    {
        $user = User::create([
            'name' => 'สมชาย ขยันงาน',
            'email' => 'somchai@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->getJson('/auth/user');

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'name' => 'สมชาย ขยันงาน',
                'email' => 'somchai@example.com',
            ],
        ]);
    }
}
