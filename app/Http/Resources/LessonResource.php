<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->users->first();

        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'duration' => $this->duration,
            'audio_url' => $this->audio_url,
            'text' => $this->text,
            'translation' => $this->translation,
            'level' => $this->level,

            'category' => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],

            'status'       => $user?->pivot?->status,
            'completed_at' => $user?->pivot?->completed_at,
        ];
    }
}
