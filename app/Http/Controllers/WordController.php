<?php

namespace App\Http\Controllers;

use App\Http\Requests\WordTranslateRequest;
use App\Services\WordService;
use Illuminate\Http\Request;

class WordController extends Controller
{
    protected WordService $wordService;

    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    public function translate(WordTranslateRequest $request)
    {
        $text = $request->validated()['text'];

        $translation = $this
            ->wordService
            ->translate($text);

        return $this->respond([
            'translation' => $translation,
        ]);
    }
}
