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

        $lessons = Lesson::query()->with([
            'category',
            'users' => fn($q) => $q->where('user_id', $user_id),
        ]);

        if ($request->has('level')) {
            $lessons->where('level', $request->level);
        }

        if ($request->boolean('only_started')) {
            $lessons->whereHas('users', fn($q) => $q->where('user_id', $user_id));
        }

        if ($request->filled('status')) {
            $lessons->whereHas('users', fn($q) => $q
                ->where('user_id', $user_id)
                ->where('lesson_user.status', $request->status));
        }

        if ($request->has('category')) {
            $lessons->where('category_id', $request->category);
        }

        return $lessons->paginate(
            $request->integer('per_page', 12)
        );
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
