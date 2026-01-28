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
        $user_id = $request->user() instanceof User
            ? $request->user()->id
            : $request->get('user_id', null);

        return Lesson::query()
            ->with('category')
            ->with(['users' => fn($q) => $q->where('user_id', $user_id)])
            ->byLevel($request->get('level'))
            ->byCategory($request->get('category'))
            ->onlyStarted($request->boolean('only_started'), $user_id)
            ->byStudyStatus($request->get('status'), $user_id)
            ->paginate($request->integer('per_page', 12));
    }

    public function start(User $user, Lesson $lesson)
    {
        if ($user->lessons()->where('lesson_id', $lesson->id)->exists()) {
            return abort('409', 'Lesson already started');
        }

        $user->lessons()->syncWithoutDetaching([
            $lesson->id => [
                'status' => LessonUserStatus::InProgress->value,
                'completed_at' => null,
            ],
        ]);
    }

    public function toggleComplete(User $user, Lesson $lesson)
    {
        $pivot = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->first()?->pivot;

        if (!$pivot) {
            abort('409', 'Lesson not started');
        }

        $is_completed = $pivot->status === LessonUserStatus::Completed->value;

        $user->lessons()->updateExistingPivot($lesson->id, [
            'status' => $is_completed
                ? LessonUserStatus::InProgress->value
                : LessonUserStatus::Completed->value,
            'completed_at' => $is_completed ? null : now(),
        ]);

        return !$is_completed;
    }
}
