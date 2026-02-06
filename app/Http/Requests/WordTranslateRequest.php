<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WordTranslateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'required|string',
        ];
    }
}
