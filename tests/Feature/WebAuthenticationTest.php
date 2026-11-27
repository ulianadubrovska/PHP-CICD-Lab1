<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_through_api(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'Уляна',
            'email' => 'lab8@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
            ->assertCreated()
            ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', [
            'email' => 'lab8@example.com',
        ]);
    }

    public function test_user_can_login_through_api(): void
    {
        $user = User::factory()->create([
            'email' => 'lab8@example.com',
            'password' => 'Password123!',
        ]);

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_invalid_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable();
    }

    public function test_user_can_logout_and_revoke_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
