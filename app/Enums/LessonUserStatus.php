<?php

namespace App\Enums;

enum LessonUserStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
