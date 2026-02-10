<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_categories(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Category::factory()->count(5)->create();

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }

    public function test_index_returns_empty_when_no_categories(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    public function test_index_returns_correct_category_data(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Travel',
            'description' => 'Travel related vocabulary',
        ]);

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $category->id,
                        'name' => 'Travel',
                        'description' => 'Travel related vocabulary',
                    ],
                ],
            ]);
    }

    public function test_index_returns_category_with_null_description(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Category::factory()->create(['description' => null]);

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertOk();
        $this->assertNull($response->json('data.0.description'));
    }
}
