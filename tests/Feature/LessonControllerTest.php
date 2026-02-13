<?php

namespace Tests\Feature;

use App\Enums\LessonUserStatus;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedUser(): User
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_'.uniqid(),
        ]);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_123',
        ]);

        return $user;
    }

    public function test_index_returns_paginated_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/lessons');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'duration',
                        'image_url',
                        'audio_url',
                        'text',
                        'translation',
                        'level',
                        'category' => ['id', 'name'],
                        'status',
                        'completed_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/lessons');

        $response->assertStatus(401);
    }

    public function test_index_filters_by_level(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->beginner()->count(2)->create();
        Lesson::factory()->advanced()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?level=beginner');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));

        foreach ($response->json('data') as $lesson) {
            $this->assertEquals('beginner', $lesson['level']);
        }
    }

    public function test_index_filters_by_category(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Lesson::factory()->count(2)->create(['category_id' => $category->id]);
        Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/lessons?category={$category->id}");

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));

        foreach ($response->json('data') as $lesson) {
            $this->assertEquals($category->id, $lesson['category']['id']);
        }
    }

    public function test_index_filters_by_search(): void
    {

        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->create(['title' => 'Learning Laravel Basics']);
        Lesson::factory()->create(['title' => 'Advanced Vue.js']);

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?search=Laravel');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('Laravel', $response->json('data.0.title'));
    }

    public function test_index_filters_only_started_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $startedLesson = Lesson::factory()->create();
        Lesson::factory()->create();

        $user->lessons()->attach($startedLesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?only_started=true');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($startedLesson->id, $response->json('data.0.id'));
    }

    public function test_index_filters_not_started_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $startedLesson = Lesson::factory()->create();
        $notStartedLesson = Lesson::factory()->create();

        $user->lessons()->attach($startedLesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?not_started=true');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($notStartedLesson->id, $response->json('data.0.id'));
    }

    public function test_index_filters_by_status(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $completedLesson = Lesson::factory()->create();
        $inProgressLesson = Lesson::factory()->create();

        $user->lessons()->attach($completedLesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);
        $user->lessons()->attach($inProgressLesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?status=completed');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($completedLesson->id, $response->json('data.0.id'));
    }

    public function test_index_respects_per_page_parameter(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/lessons?per_page=2');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        $this->assertEquals(5, $response->json('meta.total'));
    }

    public function test_index_returns_user_status_on_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)->getJson('/api/lessons');

        $response->assertOk();
        $this->assertEquals('in_progress', $response->json('data.0.status'));
    }

    public function test_index_returns_null_status_for_not_started_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/lessons');

        $response->assertOk();
        $this->assertNull($response->json('data.0.status'));
        $this->assertNull($response->json('data.0.completed_at'));
    }

    public function test_show_returns_single_lesson(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/lessons/{$lesson->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'duration',
                    'image_url',
                    'audio_url',
                    'text',
                    'translation',
                    'level',
                    'category' => ['id', 'name'],
                    'status',
                    'completed_at',
                ],
            ]);

        $this->assertEquals($lesson->id, $response->json('data.id'));
        $this->assertEquals($lesson->title, $response->json('data.title'));
    }

    public function test_show_requires_authentication(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->getJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(401);
    }

    public function test_show_returns_404_for_nonexistent_lesson(): void
    {
        $user = $this->createSubscribedUser();

        $response = $this->actingAs($user)
            ->getJson('/api/lessons/99999');

        $response->assertStatus(404);
    }

    public function test_start_marks_lesson_as_in_progress(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertOk()
            ->assertJson([
                'status' => 'in_progress',
            ]);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_start_does_not_revert_completed_lesson(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertOk();

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_start_is_idempotent_for_in_progress_lesson(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertOk()
            ->assertJson([
                'status' => 'in_progress',
            ]);

        $this->assertDatabaseCount('lesson_user', 1);
    }

    public function test_start_requires_authentication(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertStatus(401);
    }

    public function test_start_returns_404_for_nonexistent_lesson(): void
    {
        $user = $this->createSubscribedUser();

        $response = $this->actingAs($user)
            ->postJson('/api/lessons/99999/start');

        $response->assertStatus(404);
    }

    public function test_toggle_complete_marks_in_progress_lesson_as_completed(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/toggle-complete");

        $response->assertOk()
            ->assertJson([
                'completed' => true,
                'status' => 'completed',
                'message' => 'Lesson completed',
            ]);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_toggle_complete_reverts_completed_lesson_to_in_progress(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/toggle-complete");

        $response->assertOk()
            ->assertJson([
                'completed' => false,
                'status' => 'in_progress',
                'message' => 'Lesson marked as in progress',
            ]);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_toggle_complete_fails_when_lesson_not_started(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/toggle-complete");

        $response->assertStatus(404);
    }

    public function test_toggle_complete_requires_authentication(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->postJson("/api/lessons/{$lesson->id}/toggle-complete");

        $response->assertStatus(401);
    }

    public function test_toggle_complete_returns_404_for_nonexistent_lesson(): void
    {
        $user = $this->createSubscribedUser();

        $response = $this->actingAs($user)
            ->postJson('/api/lessons/99999/toggle-complete');

        $response->assertStatus(404);
    }
}
