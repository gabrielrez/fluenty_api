<?php

namespace App\Services;

use App\Adapters\LibreTranslateAPI;
use App\Interfaces\TranslateInterface;
use Illuminate\Http\Request;

class WordService
{
    protected TranslateInterface $translateAPI;

    public function __construct(LibreTranslateAPI $translateAPI)
    {
        $this->translateAPI = $translateAPI;
    }

    public function filter(Request $request)
    {
        return $request->user()
            ->savedWords()
            ->with('lesson')
            ->bySearch($request->get('search'))
            ->get();
    }

    public function translate(string $text)
    {
        return $this
            ->translateAPI
            ->translate($text);
    }
}
