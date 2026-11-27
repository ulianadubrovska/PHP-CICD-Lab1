<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_users_page_displays_users(): void
    {
        $user = User::factory()->create();

        $this->get('/shop/users')
            ->assertOk()
            ->assertSeeText($user->name)
            ->assertSeeText($user->email);
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get('/shop/users/create')
            ->assertOk()
            ->assertSeeText('Додати покупця');
    }

    public function test_can_create_user_with_hashed_password(): void
    {
        $this->post('/shop/users', [
            'name' => 'Тестова покупчиня',
            'email' => 'buyer-lab7@example.com',
            'password' => 'TestPass123!',
        ])
            ->assertRedirect(route('shop.users.index'))
            ->assertSessionHas('success');

        $user = User::where(
            'email',
            'buyer-lab7@example.com'
        )->firstOrFail();

        $this->assertTrue(
            Hash::check('TestPass123!', $user->password)
        );
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/shop/users', [
            'name' => 'Інший покупець',
            'email' => $user->email,
            'password' => 'TestPass123!',
        ])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 2);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create();

        $this->put("/shop/users/{$user->id}", [
            'name' => 'Оновлене ім’я',
            'email' => $user->email,
            'password' => '',
        ])
            ->assertRedirect(route('shop.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Оновлене ім’я',
        ]);
    }

    public function test_can_delete_user_without_relations(): void
    {
        $user = User::factory()->create();

        $this->delete("/shop/users/{$user->id}")
            ->assertRedirect(route('shop.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_cannot_delete_user_with_orders(): void
    {
        $user = User::factory()->create();

        Order::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->delete("/shop/users/{$user->id}")
            ->assertRedirect(route('shop.users.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
