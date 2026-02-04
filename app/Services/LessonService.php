<?php

namespace App\Services;

use App\Enums\LessonUserStatus;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class LessonService
{
    public function filter(Request $request)
    {
        $user = $request->user();

        return Lesson::query()
            ->with('category')
            ->with(['users' => fn($q) => $q->where('user_id', $user->id)])
            ->byLevel($request->get('level'))
            ->byCategory($request->get('category'))
            ->onlyStarted($request->boolean('only_started'), $user)
            ->notStarted($request->boolean('not_started'), $user)
            ->byStudyStatus($request->get('status'), $user)
            ->bySearch($request->get('search'))
            ->orderByUserLatestProgress($request->boolean('latest'), $user)
            ->paginate($request->integer('per_page', 12));
    }

    public function start(User $user, Lesson $lesson): void
    {
        $user_lesson = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($user_lesson && $user_lesson->pivot->status === LessonUserStatus::Completed->value) {
            return;
        }

        $user->lessons()->syncWithoutDetaching([
            $lesson->id => [
                'status' => LessonUserStatus::InProgress->value,
                'completed_at' => null,
            ],
        ]);
    }

    public function toggleComplete(User $user, Lesson $lesson): bool
    {
        $user_lesson = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        $is_currently_completed = $user_lesson->pivot->status === LessonUserStatus::Completed->value;

        $new_status = $is_currently_completed
            ? LessonUserStatus::InProgress
            : LessonUserStatus::Completed;

        $user->lessons()->updateExistingPivot($lesson->id, [
            'status' => $new_status->value,
            'completed_at' => $new_status === LessonUserStatus::Completed ? now() : null,
        ]);

        return $new_status === LessonUserStatus::Completed;
    }
}
