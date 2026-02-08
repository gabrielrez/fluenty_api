<?php

namespace App\Http\Controllers;

use App\Http\Requests\WordSaveRequest;
use App\Http\Requests\WordTranslateRequest;
use App\Http\Resources\SavedWordResourse;
use App\Models\SavedWord;
use App\Services\WordService;
use Illuminate\Http\Request;

class WordController extends Controller
{
    protected WordService $wordService;

    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    public function index(Request $request)
    {
        $words = $request->user()
            ->savedWords()
            ->get();

        return $this->respond(SavedWordResourse::collection($words));
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

    public function store(WordSaveRequest $request)
    {
        $request->user()
            ->savedWords()
            ->firstOrCreate($request->validated());

        return $this->respond('Word saved');
    }

    public function delete(SavedWord $savedWord)
    {
        $this->authorize('delete', $savedWord);

        $savedWord->delete();

        return $this->respond('Word removed');
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
