<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WordSaveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'word' => 'required|string',
            'context' => 'required|string',
            'translation' => 'required|string',
        ];
    }
}
