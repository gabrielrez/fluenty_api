<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class LessonService
{
    public function filter(Request $request)
    {
        $user = $request->user();

        $lessons = Lesson::query()
            ->with(['language', 'progress']);

        if ($request->has('language')) {
            $lessons->byLanguage($request->language);
        }

        if ($request->boolean('only_started')) {
            $lessons->whereHas('users', fn($q) => $q->where('users.id', $user->id));
        }

        if ($request->has('status')) {
            $lessons->whereHas('users', fn($q) => $q
                ->where('users.id', $user->id)
                ->where('lesson_user.status', $request->status));
        }

        return $lessons->paginate(
            $request->get('per_page', 12)
        );
    }
}
