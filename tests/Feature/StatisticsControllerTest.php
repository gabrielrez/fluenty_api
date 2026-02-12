<?php

namespace Tests\Feature;

use App\Enums\LessonUserStatus;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_requires_authentication(): void
    {
        $response = $this->getJson('/api/statistics');

        $response->assertStatus(401);
    }

    public function test_statistics_returns_correct_structure(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'percent_progress',
                    'completed_lessons',
                    'words_saved',
                    'study_time',
                    'sequence',
                ],
            ]);
    }

    public function test_statistics_returns_zeros_for_new_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Lesson::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    'percent_progress' => 0,
                    'completed_lessons' => 0,
                    'words_saved' => 0,
                    'study_time' => 0,
                    'sequence' => 1,
                ],
            ]);
    }

    public function test_statistics_returns_correct_percent_progress(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(4)->create(['category_id' => $category->id]);

        $user->lessons()->attach($lessons[0]->id, ['status' => LessonUserStatus::InProgress->value]);
        $user->lessons()->attach($lessons[1]->id, ['status' => LessonUserStatus::Completed->value]);

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk();
        $this->assertEquals(50, $response->json('data.percent_progress'));
    }

    public function test_statistics_returns_correct_completed_lessons(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(3)->create(['category_id' => $category->id]);

        $user->lessons()->attach($lessons[0]->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lessons[1]->id, ['status' => LessonUserStatus::Completed->value]);
        $user->lessons()->attach($lessons[2]->id, ['status' => LessonUserStatus::InProgress->value]);

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk();
        $this->assertEquals(2, $response->json('data.completed_lessons'));
    }

    public function test_statistics_returns_correct_words_saved(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        SavedWord::factory()->count(4)->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk();
        $this->assertEquals(4, $response->json('data.words_saved'));
    }

    public function test_statistics_returns_correct_study_time(): void
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

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk();
        $this->assertEquals(300, $response->json('data.study_time'));
    }

    public function test_statistics_returns_full_progress_when_all_lessons_completed(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $lessons = Lesson::factory()->count(2)->create(['category_id' => $category->id]);

        foreach ($lessons as $lesson) {
            $user->lessons()->attach($lesson->id, ['status' => LessonUserStatus::Completed->value]);
        }

        $response = $this->actingAs($user)->getJson('/api/statistics');

        $response->assertOk();
        $this->assertEquals(100, $response->json('data.percent_progress'));
        $this->assertEquals(2, $response->json('data.completed_lessons'));
    }

    public function test_statistics_does_not_include_other_users_data(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        $category = Category::factory()->create();
        $lesson = Lesson::factory()->create(['category_id' => $category->id, 'duration' => 100]);

        $user1->lessons()->attach($lesson->id, ['status' => LessonUserStatus::Completed->value]);
        SavedWord::factory()->count(3)->create(['user_id' => $user1->id, 'lesson_id' => $lesson->id]);

        $response = $this->actingAs($user2)->getJson('/api/statistics');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    'percent_progress' => 0,
                    'completed_lessons' => 0,
                    'words_saved' => 0,
                    'study_time' => 0,
                    'sequence' => 1,
                ],
            ]);
    }
}
