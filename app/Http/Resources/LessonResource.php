<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        $authUserId = $request->user()?->id;

        $pivot = $this->users->firstWhere('id', $authUserId)?->pivot;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $this->duration,
            'image_url' => $this->image_url,
            'audio_url' => $this->audio_url,
            'text' => $this->text,
            'translation' => $this->translation,
            'level' => $this->level,

            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],

            'source' => $this->source,

            'status' => $pivot?->status,
            'completed_at' => $pivot?->completed_at,
        ];
    }
}
