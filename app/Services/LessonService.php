<?php

namespace App\Services;

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
}
