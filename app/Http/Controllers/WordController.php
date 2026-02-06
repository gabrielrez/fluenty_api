<?php

namespace App\Http\Controllers;

use App\Http\Requests\WordSaveRequest;
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

    public function show(Request $request, string $word)
    {
        $savedWord = $request->user()
            ->savedWords()
            ->where('word', $word)
            ->first();

        return $this->respond([
            'word' => $word,
            'saved' => (bool) $savedWord,
            'translation' => $savedWord?->translation,
            'context' => $savedWord?->context,
        ]);
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

    public function toggleSave(WordSaveRequest $request)
    {
        $user = $request->user();

        $savedWord = $user->savedWords()
            ->where('word', $request->word)
            ->first();

        if ($savedWord) {
            $savedWord->delete();

            return $this->respond([
                'saved' => false,
            ]);
        }

        $user->savedWords()->create($request->validated());

        return $this->respond([
            'saved' => true,
        ]);
    }
}
