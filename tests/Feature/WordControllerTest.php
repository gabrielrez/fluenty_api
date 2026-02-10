<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use App\Services\WordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_user_saved_words(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        SavedWord::factory()->count(3)->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/words');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'word',
                        'translation',
                        'context',
                        'created_at',
                        'lesson',
                    ],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/words');

        $response->assertStatus(401);
    }

    public function test_index_only_returns_authenticated_users_words(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        SavedWord::factory()->count(2)->create(['user_id' => $user->id]);
        SavedWord::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/words');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_search(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        SavedWord::factory()->create([
            'user_id' => $user->id,
            'word' => 'beautiful',
        ]);
        SavedWord::factory()->create([
            'user_id' => $user->id,
            'word' => 'morning',
        ]);

        $response = $this->actingAs($user)->getJson('/api/words?search=beautiful');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('beautiful', $response->json('data.0.word'));
    }

    public function test_index_includes_lesson_relation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        SavedWord::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/words');

        $response->assertOk();
        $this->assertNotNull($response->json('data.0.lesson'));
        $this->assertEquals($lesson->id, $response->json('data.0.lesson.id'));
    }

    public function test_show_returns_saved_word_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $savedWord = SavedWord::factory()->create([
            'user_id' => $user->id,
            'word' => 'hello',
            'translation' => 'olá',
            'context' => 'Hello, how are you?',
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/words/hello?context=Hello, how are you?");

        $response->assertOk()
            ->assertJson([
                'word' => 'hello',
                'saved' => true,
                'translation' => 'olá',
                'context' => 'Hello, how are you?',
            ]);
    }

    public function test_show_returns_not_saved_when_word_not_found(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/words/nonexistent?context=some context');

        $response->assertOk()
            ->assertJson([
                'word' => 'nonexistent',
                'saved' => false,
                'translation' => null,
                'context' => null,
            ]);
    }

    public function test_show_requires_authentication(): void
    {
        $response = $this->getJson('/api/words/hello');

        $response->assertStatus(401);
    }

    public function test_translate_returns_translation(): void
    {
        $this->mock(WordService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->with('hello')
                ->once()
                ->andReturn('olá');
        });

        $response = $this->postJson('/api/words/translate', [
            'text' => 'hello',
        ]);

        $response->assertOk()
            ->assertJson([
                'translation' => 'olá',
            ]);
    }

    public function test_translate_requires_text_field(): void
    {
        $response = $this->postJson('/api/words/translate', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_translate_does_not_require_authentication(): void
    {
        $this->mock(WordService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->once()
                ->andReturn('tradução');
        });

        $response = $this->postJson('/api/words/translate', [
            'text' => 'translation',
        ]);

        $response->assertOk();
    }

    public function test_store_saves_word_for_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/words/save', [
            'word' => 'beautiful',
            'translation' => 'bonito',
            'context' => 'What a beautiful day',
            'lesson_id' => $lesson->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('saved_words', [
            'user_id' => $user->id,
            'word' => 'beautiful',
            'translation' => 'bonito',
            'context' => 'What a beautiful day',
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_store_does_not_duplicate_existing_word(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $data = [
            'word' => 'beautiful',
            'translation' => 'bonito',
            'context' => 'What a beautiful day',
            'lesson_id' => $lesson->id,
        ];

        $this->actingAs($user)->postJson('/api/words/save', $data);
        $this->actingAs($user)->postJson('/api/words/save', $data);

        $this->assertDatabaseCount('saved_words', 1);
    }

    public function test_store_requires_authentication(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->postJson('/api/words/save', [
            'word' => 'hello',
            'translation' => 'olá',
            'context' => 'Hello world',
            'lesson_id' => $lesson->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_store_validates_required_fields(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/words/save', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['word', 'translation', 'context', 'lesson_id']);
    }

    public function test_store_validates_lesson_exists(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/words/save', [
            'word' => 'hello',
            'translation' => 'olá',
            'context' => 'Hello world',
            'lesson_id' => 99999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lesson_id']);
    }

    public function test_delete_removes_saved_word(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $savedWord = SavedWord::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/words/{$savedWord->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Word removed',
            ]);

        $this->assertDatabaseMissing('saved_words', [
            'id' => $savedWord->id,
        ]);
    }

    public function test_delete_requires_authentication(): void
    {
        $savedWord = SavedWord::factory()->create();

        $response = $this->deleteJson("/api/words/{$savedWord->id}");

        $response->assertStatus(401);
    }

    public function test_delete_forbids_deleting_other_users_word(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $savedWord = SavedWord::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/words/{$savedWord->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('saved_words', [
            'id' => $savedWord->id,
        ]);
    }

    public function test_toggle_save_creates_word_when_not_saved(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/words/toggle', [
            'word' => 'amazing',
            'translation' => 'incrível',
            'context' => 'That was amazing',
            'lesson_id' => $lesson->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'saved' => true,
            ]);

        $this->assertDatabaseHas('saved_words', [
            'user_id' => $user->id,
            'word' => 'amazing',
        ]);
    }

    public function test_toggle_save_removes_word_when_already_saved(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        SavedWord::factory()->create([
            'user_id' => $user->id,
            'word' => 'amazing',
            'lesson_id' => $lesson->id,
        ]);

        $response = $this->actingAs($user)->postJson('/api/words/toggle', [
            'word' => 'amazing',
            'translation' => 'incrível',
            'context' => 'That was amazing',
            'lesson_id' => $lesson->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'saved' => false,
            ]);

        $this->assertDatabaseMissing('saved_words', [
            'user_id' => $user->id,
            'word' => 'amazing',
        ]);
    }

    public function test_toggle_save_requires_authentication(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->postJson('/api/words/toggle', [
            'word' => 'hello',
            'translation' => 'olá',
            'context' => 'Hello world',
            'lesson_id' => $lesson->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_toggle_save_validates_required_fields(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/words/toggle', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['word', 'translation', 'context', 'lesson_id']);
    }
}
