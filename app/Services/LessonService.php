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
            ->with(['users' => fn ($q) => $q->where('user_id', $user->id)])
            ->byLevel($request->get('level'))
            ->byCategory($request->get('category'))
            ->isFree($request->hasAny('is_free') ? $request->boolean('is_free') : null)
            ->onlyStarted($request->boolean('only_started'), $user)
            ->notStarted($request->boolean('not_started'), $user)
            ->byStudyStatus($request->get('status'), $user)
            ->bySearch($request->get('search'))
            ->orderByUserLatestProgress($request->boolean('latest'), $user)
            ->paginate($request->integer('per_page', 12));
    }

    public function start(User $user, Lesson $lesson): void
    {
        $userLesson = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($userLesson && $userLesson->pivot->status === LessonUserStatus::Completed->value) {
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
        $userLesson = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        $isCurrentlyCompleted = $userLesson->pivot->status === LessonUserStatus::Completed->value;

        $newStatus = $isCurrentlyCompleted
            ? LessonUserStatus::InProgress
            : LessonUserStatus::Completed;

        $user->lessons()->updateExistingPivot($lesson->id, [
            'status' => $newStatus->value,
            'completed_at' => $newStatus === LessonUserStatus::Completed ? now() : null,
        ]);

        return $newStatus === LessonUserStatus::Completed;
    }
}
