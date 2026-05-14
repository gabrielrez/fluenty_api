<?php

namespace App\Http\Controllers;

use App\Enums\LessonUserStatus;
use App\Http\Requests\LessonStoreRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Services\LessonService;
use App\Services\SequenceService;
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

    public function show(Lesson $lesson)
    {
        return $this->respond(new LessonResource($lesson));
    }

    public function store(LessonStoreRequest $request)
    {
        $lesson = Lesson::create($request->validated());

        return $this->respondCreated(new LessonResource($lesson));
    }

    public function start(Request $request, Lesson $lesson)
    {
        $this->lessonService->start($request->user(), $lesson);

        return $this->respond([
            'status' => LessonUserStatus::InProgress->value,
        ]);
    }

    public function toggleComplete(Request $request, Lesson $lesson)
    {
        $completed = $this->lessonService->toggleComplete(
            $request->user(),
            $lesson
        );

        if ($completed) {
            app(SequenceService::class)->handle($request->user());
        }

        return response()->json([
            'completed' => $completed,
            'status' => $completed ? 'completed' : 'in_progress',
            'message' => $completed
                ? 'Lesson completed'
                : 'Lesson marked as in progress',
        ]);
    }
}
