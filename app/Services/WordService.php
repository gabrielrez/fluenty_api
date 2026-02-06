<?php

namespace App\Services;

use App\Adapters\LibreTranslateAPI;
use App\Interfaces\TranslateInterface;
use Illuminate\Support\Facades\Http;

class WordService
{
    protected TranslateInterface $translateAPI;

    public function __construct(LibreTranslateAPI $translateAPI)
    {
        $this->translateAPI = $translateAPI;
    }

    public function translate(string $text)
    {
        return $this
            ->translateAPI
            ->translate($text);
    }
}
