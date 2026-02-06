<?php

namespace App\Interfaces;

interface TranslateInterface
{
    public function translate(string $text, ?string $source = 'en', ?string $target = 'pt');
}
