<?php

namespace App\Http\Controllers;

use App\Enums\LessonUserStatus;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Services\LessonService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    protected LessonService $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function index(Request $request)
    {
        return $this->respond(LessonResource::collection(
            $this->lessonService->filter($request)
        ));
    }

    public function start(Request $request, Lesson $lesson)
    {
        $this->lessonService->start($request->user(), $lesson);

        return $this->respond('Lesson started');
    }

    public function toggleComplete(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        $completed = $this->lessonService->toggleComplete($user, $lesson);

        return $this->respond(
            $completed
                ? 'Lesson completed'
                : 'Lesson uncompleted'
        );
    }
}
