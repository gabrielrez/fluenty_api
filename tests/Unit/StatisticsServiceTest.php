<?php

namespace Tests\Unit;

use App\Enums\LessonUserStatus;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private StatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticsService;
    }

    public function test_calc_returns_correct_structure(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->create();

        $result = $this->service->calc($user);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('percent_progress', $result);
        $this->assertArrayHasKey('completed_lessons', $result);
        $this->assertArrayHasKey('words_saved', $result);
        $this->assertArrayHasKey('study_time', $result);
        $this->assertArrayHasKey('sequence', $result);
        $this->assertCount(5, $result);
    }

    public function test_calc_returns_zero_values_for_new_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->count(3)->create();

        $result = $this->service->calc($user);

        $this->assertEquals(0, $result['percent_progress']);
        $this->assertEquals(0, $result['completed_lessons']);
        $this->assertEquals(0, $result['words_saved']);
        $this->assertEquals(0, $result['study_time']);
    }

    public function test_calc_percent_progress_with_no_lessons_started(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->count(5)->create();

        $result = $this->service->calc($user);

        $this->assertEquals(0, $result['percent_progress']);
    }

    public function test_calc_percent_progress_with_some_lessons_started(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(4)->create(['category_id' => $category->id]);

        $user->lessons()->attach($lessons[0]->id, ['status' => LessonUserStatus::InProgress->value]);
        $user->lessons()->attach($lessons[1]->id, ['status' => LessonUserStatus::Completed->value]);

        $result = $this->service->calc($user);

        $this->assertEquals(50, $result['percent_progress']);
    }

    public function test_calc_percent_progress_with_all_lessons_started(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(3)->create(['category_id' => $category->id]);

        foreach ($lessons as $lesson) {
            $user->lessons()->attach($lesson->id, ['status' => LessonUserStatus::Completed->value]);
        }

        $result = $this->service->calc($user);

        $this->assertEquals(100, $result['percent_progress']);
    }

    public function test_calc_completed_lessons_count(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(3)->create(['category_id' => $category->id]);

        $user->lessons()->attach($lessons[0]->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lessons[1]->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lessons[2]->id, ['status' => LessonUserStatus::InProgress->value]);

        $result = $this->service->calc($user);

        $this->assertEquals(2, $result['completed_lessons']);
    }

    public function test_calc_words_saved_count(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        SavedWord::factory()->count(5)->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        SavedWord::factory()->count(3)->create([
            'lesson_id' => $lesson->id,
        ]);

        $result = $this->service->calc($user);

        $this->assertEquals(5, $result['words_saved']);
    }

    public function test_calc_study_time_sums_completed_lesson_durations(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $lesson1 = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 120]);
        $lesson2 = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 180]);
        $lesson3 = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 300]);

        $user->lessons()->attach($lesson1->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lesson2->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lesson3->id, ['status' => LessonUserStatus::InProgress->value]);

        $result = $this->service->calc($user);

        $this->assertEquals(300, $result['study_time']);
    }

    public function test_calc_study_time_excludes_in_progress_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $lesson = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 200]);
        $user->lessons()->attach($lesson->id, ['status' => LessonUserStatus::InProgress->value]);

        $result = $this->service->calc($user);

        $this->assertEquals(0, $result['study_time']);
    }

    public function test_calc_isolates_data_between_users(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        $category = Category::factory()->create();

        $lesson = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 100]);

        $user1->lessons()->attach($lesson->id, ['status' => LessonUserStatus::Completed->value]);
        SavedWord::factory()->count(3)->create(['user_id' => $user1->id, 'lesson_id' => $lesson->id]);

        $result = $this->service->calc($user2);

        $this->assertEquals(0, $result['percent_progress']);
        $this->assertEquals(0, $result['completed_lessons']);
        $this->assertEquals(0, $result['words_saved']);
        $this->assertEquals(0, $result['study_time']);
    }
}
