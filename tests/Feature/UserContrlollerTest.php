<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserContrlollerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_returns_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);
    }

    public function test_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_update_name(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'name' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => 'New Name',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_update_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_update_name_and_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Old Name',
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'name' => 'New Name',
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => 'New Name',
                ],
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/me/update', [
            'name' => 'New Name',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_password_requires_current_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_update_password_fails_with_wrong_current_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_update_password_requires_confirmation(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_update_password_requires_minimum_length(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'oldpassword123',
        ]);

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'current_password' => 'oldpassword123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_update_name_max_length(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/me/update', [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
