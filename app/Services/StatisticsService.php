<?php

namespace App\Services;

use App\Enums\LessonUserStatus;
use App\Models\Lesson;
use App\Models\User;

class StatisticsService
{
    public function calc(User $user): array
    {
        $percentProgress = ($user->lessons()->count() / Lesson::count()) * 100;

        $lessonsCompleted = $user
            ->completedLessons()
            ->count();

        $wordsSaved = $user
            ->savedWords()
            ->count();

        $studyTime = Lesson::query()
            ->byStudyStatus(LessonUserStatus::Completed->value, $user)
            ->pluck('duration')
            ->sum();

        return [
            'percent_progress' => $percentProgress,
            'completed_lessons' => $lessonsCompleted,
            'words_saved' => $wordsSaved,
            'study_time' => $studyTime,
            'sequence' => $user->sequence,
        ];
    }
}
