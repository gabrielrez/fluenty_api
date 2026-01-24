<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
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
}
