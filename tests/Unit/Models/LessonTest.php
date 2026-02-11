<?php

namespace Tests\Unit\Models;

use App\Enums\LessonLevelEnum;
use App\Enums\LessonUserStatus;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $lesson = new Lesson;

        $this->assertEquals(
            ['title', 'description', 'text', 'translation', 'image_url', 'audio_url', 'duration', 'category_id', 'level'],
            $lesson->getFillable()
        );
    }

    public function test_casts_attributes(): void
    {
        $lesson = new Lesson;
        $casts = $lesson->getCasts();

        $this->assertEquals('integer', $casts['duration']);
        $this->assertEquals(LessonLevelEnum::class, $casts['level']);
    }

    public function test_level_is_cast_to_enum(): void
    {
        $lesson = Lesson::factory()->beginner()->create();

        $this->assertInstanceOf(LessonLevelEnum::class, $lesson->level);
        $this->assertEquals(LessonLevelEnum::Beginner, $lesson->level);
    }

    public function test_duration_is_cast_to_integer(): void
    {
        $lesson = Lesson::factory()->create(['duration' => '120']);

        $this->assertIsInt($lesson->duration);
        $this->assertEquals(120, $lesson->duration);
    }

    public function test_text_accessor_converts_escaped_newlines(): void
    {
        $lesson = Lesson::factory()->create(['text' => 'Hello\\nWorld']);

        $this->assertEquals("Hello\nWorld", $lesson->text);
    }

    public function test_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $lesson = Lesson::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $lesson->category);
        $this->assertEquals($category->id, $lesson->category->id);
    }

    public function test_belongs_to_many_users(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $lesson->users()->attach($user->id, ['status' => 'in_progress']);

        $this->assertCount(1, $lesson->users);
        $this->assertInstanceOf(User::class, $lesson->users->first());
    }

    public function test_has_many_saved_words(): void
    {
        $lesson = Lesson::factory()->create();
        SavedWord::factory()->count(2)->create(['lesson_id' => $lesson->id]);

        $this->assertCount(2, $lesson->savedWords);
        $this->assertInstanceOf(SavedWord::class, $lesson->savedWords->first());
    }

    public function test_scope_by_level(): void
    {
        Lesson::factory()->beginner()->create();
        Lesson::factory()->advanced()->create();

        $results = Lesson::byLevel(LessonLevelEnum::Beginner->value)->get();

        $this->assertCount(1, $results);
        $this->assertEquals(LessonLevelEnum::Beginner, $results->first()->level);
    }

    public function test_scope_by_level_returns_all_when_null(): void
    {
        Lesson::factory()->beginner()->create();
        Lesson::factory()->advanced()->create();

        $results = Lesson::byLevel(null)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_by_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Lesson::factory()->create(['category_id' => $category1->id]);
        Lesson::factory()->create(['category_id' => $category2->id]);

        $results = Lesson::byCategory($category1->id)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->category_id);
    }

    public function test_scope_by_category_returns_all_when_null(): void
    {
        Lesson::factory()->count(2)->create();

        $results = Lesson::byCategory(null)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_only_started(): void
    {
        $user = User::factory()->create();
        $started = Lesson::factory()->create();
        $notStarted = Lesson::factory()->create();

        $user->lessons()->attach($started->id, ['status' => 'in_progress']);

        $results = Lesson::onlyStarted(true, $user)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($started->id, $results->first()->id);
    }

    public function test_scope_only_started_returns_all_when_false(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(2)->create();

        $results = Lesson::onlyStarted(false, $user)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_not_started(): void
    {
        $user = User::factory()->create();
        $started = Lesson::factory()->create();
        $notStarted = Lesson::factory()->create();

        $user->lessons()->attach($started->id, ['status' => 'in_progress']);

        $results = Lesson::notStarted(true, $user)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($notStarted->id, $results->first()->id);
    }

    public function test_scope_not_started_returns_all_when_false(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(2)->create();

        $results = Lesson::notStarted(false, $user)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_by_study_status(): void
    {
        $user = User::factory()->create();
        $completed = Lesson::factory()->create();
        $inProgress = Lesson::factory()->create();

        $user->lessons()->attach($completed->id, [
            'status' => LessonUserStatus::Completed->value,
        ]);
        $user->lessons()->attach($inProgress->id, [
            'status' => LessonUserStatus::InProgress->value,
        ]);

        $results = Lesson::byStudyStatus(LessonUserStatus::Completed->value, $user)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($completed->id, $results->first()->id);
    }

    public function test_scope_by_study_status_returns_all_when_null(): void
    {
        Lesson::factory()->count(2)->create();

        $results = Lesson::byStudyStatus(null, null)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_by_search(): void
    {
        Lesson::factory()->create(['title' => 'Greetings in English']);
        Lesson::factory()->create(['title' => 'Colors and shapes']);

        $results = Lesson::bySearch('Greetings')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Greetings in English', $results->first()->title);
    }

    public function test_scope_by_search_matches_description(): void
    {
        Lesson::factory()->create([
            'title' => 'Lesson One',
            'description' => 'Learn about animals',
        ]);
        Lesson::factory()->create([
            'title' => 'Lesson Two',
            'description' => 'Learn about colors',
        ]);

        $results = Lesson::bySearch('animals')->get();

        $this->assertCount(1, $results);
    }

    public function test_scope_by_search_returns_all_when_null(): void
    {
        Lesson::factory()->count(2)->create();

        $results = Lesson::bySearch(null)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_order_by_user_latest_progress(): void
    {
        $user = User::factory()->create();
        $lesson1 = Lesson::factory()->create();
        $lesson2 = Lesson::factory()->create();

        $user->lessons()->attach($lesson1->id, [
            'status' => 'in_progress',
            'created_at' => now()->subDay(),
        ]);
        $user->lessons()->attach($lesson2->id, [
            'status' => 'in_progress',
            'created_at' => now(),
        ]);

        $results = Lesson::orderByUserLatestProgress(true, $user)->get();

        $this->assertEquals($lesson2->id, $results->first()->id);
        $this->assertEquals($lesson1->id, $results->last()->id);
    }

    public function test_scope_order_by_user_latest_progress_returns_all_when_false(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(2)->create();

        $results = Lesson::orderByUserLatestProgress(false, $user)->get();

        $this->assertCount(2, $results);
    }
}
