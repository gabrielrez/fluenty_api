<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedWordTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $savedWord = new SavedWord();

        $this->assertEquals(
            ['user_id', 'lesson_id', 'word', 'translation', 'context'],
            $savedWord->getFillable()
        );
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $savedWord = SavedWord::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $savedWord->user);
        $this->assertEquals($user->id, $savedWord->user->id);
    }

    public function test_belongs_to_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $savedWord = SavedWord::factory()->create(['lesson_id' => $lesson->id]);

        $this->assertInstanceOf(Lesson::class, $savedWord->lesson);
        $this->assertEquals($lesson->id, $savedWord->lesson->id);
    }

    public function test_can_be_created_with_factory(): void
    {
        $savedWord = SavedWord::factory()->create();

        $this->assertDatabaseHas('saved_words', [
            'id' => $savedWord->id,
            'word' => $savedWord->word,
        ]);
    }

    public function test_scope_by_search(): void
    {
        SavedWord::factory()->create(['word' => 'apple']);
        SavedWord::factory()->create(['word' => 'banana']);

        $results = SavedWord::bySearch('apple')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('apple', $results->first()->word);
    }

    public function test_scope_by_search_with_partial_match(): void
    {
        SavedWord::factory()->create(['word' => 'pineapple']);
        SavedWord::factory()->create(['word' => 'banana']);

        $results = SavedWord::bySearch('apple')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('pineapple', $results->first()->word);
    }

    public function test_scope_by_search_returns_all_when_null(): void
    {
        SavedWord::factory()->count(3)->create();

        $results = SavedWord::bySearch(null)->get();

        $this->assertCount(3, $results);
    }
}
