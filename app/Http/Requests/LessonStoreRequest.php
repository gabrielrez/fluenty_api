<?php

namespace App\Http\Requests;

use App\Enums\LessonLevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'text' => ['required', 'string'],
            'translation' => ['required', 'string'],
            'image_url' => ['nullable', 'url'],
            'audio_url' => ['required', 'url'],
            'duration' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'level' => [
                'required',
                Rule::enum(LessonLevelEnum::class)
            ],
            'source' => ['required', 'string', 'max:255'],
            'is_free' => ['boolean'],
        ];
    }
}
