<?php

namespace App\Adapters;

use App\Interfaces\TranslateInterface;
use Illuminate\Support\Facades\Http;

class LibreTranslateAPI implements TranslateInterface
{
    public function translate(string $text, ?string $source = 'en', ?string $target = 'pt')
    {
        $response = Http::timeout(10)->post(config('services.libretranslate.url').'/translate', [
            'q' => $text,
            'source' => $source,
            'target' => $target,
            'format' => 'text',
        ]);

        return $response->json('translatedText');
    }
}
