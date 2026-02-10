<?php

namespace Tests\Unit\Models;

use App\Enums\LessonUserStatus;
use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $user = new User();

        $this->assertEquals(
            ['name', 'email', 'password', 'sequence', 'avatar'],
            $user->getFillable()
        );
    }

    public function test_hidden_attributes(): void
    {
        $user = new User();

        $this->assertEquals(
            ['password', 'remember_token'],
            $user->getHidden()
        );
    }

    public function test_casts_attributes(): void
    {
        $user = new User();
        $casts = $user->getCasts();

        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('integer', $casts['sequence']);
    }

    public function test_password_is_hashed_on_set(): void
    {
        $user = User::factory()->create(['password' => 'plain-text']);

        $this->assertTrue(Hash::check('plain-text', $user->password));
        $this->assertNotEquals('plain-text', $user->password);
    }

    public function test_already_hashed_password_is_not_rehashed(): void
    {
        $hashed = Hash::make('my-password');
        $user = User::factory()->create(['password' => $hashed]);

        $this->assertEquals($hashed, $user->password);
    }

    public function test_has_many_saved_words(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->savedWords);
        $this->assertInstanceOf(SavedWord::class, $user->savedWords->first());
    }

    public function test_belongs_to_many_lessons(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, ['status' => 'in_progress']);

        $this->assertCount(1, $user->lessons);
        $this->assertInstanceOf(Lesson::class, $user->lessons->first());
    }

    public function test_lessons_pivot_has_status_and_completed_at(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, [
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $pivot = $user->lessons->first()->pivot;

        $this->assertEquals('completed', $pivot->status);
        $this->assertNotNull($pivot->completed_at);
        $this->assertNotNull($pivot->created_at);
        $this->assertNotNull($pivot->updated_at);
    }

    public function test_completed_lessons_returns_only_completed(): void
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

        $completedLessons = $user->completedLessons()->get();

        $this->assertCount(1, $completedLessons);
        $this->assertEquals($completed->id, $completedLessons->first()->id);
    }

    public function test_in_progress_lessons_returns_only_in_progress(): void
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

        $inProgressLessons = $user->inProgressLessons()->get();

        $this->assertCount(1, $inProgressLessons);
        $this->assertEquals($inProgress->id, $inProgressLessons->first()->id);
    }

    public function test_sequence_is_cast_to_integer(): void
    {
        $user = User::factory()->create(['sequence' => '5']);

        $this->assertIsInt($user->sequence);
        $this->assertEquals(5, $user->sequence);
    }
}
