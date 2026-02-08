<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedWordResourse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'word' => $this->word,
            'translation' => $this->translation,
            'context' => $this->context,
            'created_at' => $this->created_at,
        ];
    }
}
