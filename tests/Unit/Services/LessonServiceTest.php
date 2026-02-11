<?php

namespace Tests\Unit\Services;

use App\Enums\LessonLevelEnum;
use App\Enums\LessonUserStatus;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class LessonServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LessonService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LessonService;
    }

    public function test_filter_returns_paginated_lessons(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(3)->create();

        $request = Request::create('/api/lessons', 'GET');
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(3, $result->items());
        $this->assertEquals(3, $result->total());
    }

    public function test_filter_eager_loads_category(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->create();

        $request = Request::create('/api/lessons', 'GET');
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);
        $lesson = $result->items()[0];

        $this->assertTrue($lesson->relationLoaded('category'));
    }

    public function test_filter_eager_loads_user_relationship(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $request = Request::create('/api/lessons', 'GET');
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);
        $loadedLesson = $result->items()[0];

        $this->assertTrue($loadedLesson->relationLoaded('users'));
        $this->assertCount(1, $loadedLesson->users);
    }

    public function test_filter_by_level(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->beginner()->count(2)->create();
        Lesson::factory()->advanced()->create();

        $request = Request::create('/api/lessons', 'GET', ['level' => 'beginner']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(2, $result->items());
        foreach ($result->items() as $lesson) {
            $this->assertEquals(LessonLevelEnum::Beginner, $lesson->level);
        }
    }

    public function test_filter_by_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Lesson::factory()->count(2)->create(['category_id' => $category->id]);
        Lesson::factory()->create();

        $request = Request::create('/api/lessons', 'GET', ['category' => $category->id]);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(2, $result->items());
    }

    public function test_filter_by_search(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->create(['title' => 'PHP Unit Testing Guide']);
        Lesson::factory()->create(['title' => 'JavaScript Basics']);

        $request = Request::create('/api/lessons', 'GET', ['search' => 'PHP']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(1, $result->items());
        $this->assertStringContainsString('PHP', $result->items()[0]->title);
    }

    public function test_filter_only_started(): void
    {
        $user = User::factory()->create();
        $startedLesson = Lesson::factory()->create();
        Lesson::factory()->create();

        $user->lessons()->attach($startedLesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $request = Request::create('/api/lessons', 'GET', ['only_started' => 'true']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(1, $result->items());
        $this->assertEquals($startedLesson->id, $result->items()[0]->id);
    }

    public function test_filter_not_started(): void
    {
        $user = User::factory()->create();
        $startedLesson = Lesson::factory()->create();
        $notStarted = Lesson::factory()->create();

        $user->lessons()->attach($startedLesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $request = Request::create('/api/lessons', 'GET', ['not_started' => 'true']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(1, $result->items());
        $this->assertEquals($notStarted->id, $result->items()[0]->id);
    }

    public function test_filter_by_study_status(): void
    {
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

        $request = Request::create('/api/lessons', 'GET', ['status' => 'completed']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(1, $result->items());
        $this->assertEquals($completedLesson->id, $result->items()[0]->id);
    }

    public function test_filter_respects_per_page(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(5)->create();

        $request = Request::create('/api/lessons', 'GET', ['per_page' => '2']);
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(2, $result->items());
        $this->assertEquals(5, $result->total());
    }

    public function test_filter_defaults_to_12_per_page(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(15)->create();

        $request = Request::create('/api/lessons', 'GET');
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(12, $result->items());
        $this->assertEquals(15, $result->total());
    }

    public function test_filter_returns_all_when_no_filters(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(3)->create();

        $request = Request::create('/api/lessons', 'GET');
        $request->setUserResolver(fn () => $user);

        $result = $this->service->filter($request);

        $this->assertCount(3, $result->items());
    }

    public function test_start_attaches_lesson_to_user_with_in_progress_status(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->service->start($user, $lesson);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
            'completed_at' => null,
        ]);
    }

    public function test_start_does_not_revert_completed_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);

        $this->service->start($user, $lesson);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_start_is_idempotent_for_in_progress_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $this->service->start($user, $lesson);

        $this->assertDatabaseCount('lesson_user', 1);
        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_start_returns_void(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $result = $this->service->start($user, $lesson);

        $this->assertNull($result);
    }

    public function test_toggle_complete_marks_in_progress_as_completed(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $result = $this->service->toggleComplete($user, $lesson);

        $this->assertTrue($result);
        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_toggle_complete_sets_completed_at_timestamp(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $this->service->toggleComplete($user, $lesson);

        $pivot = $user->lessons()->where('lesson_id', $lesson->id)->first()->pivot;
        $this->assertNotNull($pivot->completed_at);
    }

    public function test_toggle_complete_reverts_completed_to_in_progress(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);

        $result = $this->service->toggleComplete($user, $lesson);

        $this->assertFalse($result);
        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_toggle_complete_clears_completed_at_when_reverting(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => LessonUserStatus::Completed->value,
            'completed_at' => now(),
        ]);

        $this->service->toggleComplete($user, $lesson);

        $pivot = $user->lessons()->where('lesson_id', $lesson->id)->first()->pivot;
        $this->assertNull($pivot->completed_at);
    }

    public function test_toggle_complete_throws_exception_when_lesson_not_started(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        $this->service->toggleComplete($user, $lesson);
    }
}
