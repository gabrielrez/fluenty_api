<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            return [
                'percent_progress' => $this->resource['percent_progress'],
                'completed_lessons' => $this->resource['completed_lessons'],
                'words_saved' => $this->resource['words_saved'],
                'study_time' => $this->resource['study_time'],
                'sequence' => $this->resource['sequence'],
            ];
        }

        return [
            'percent_progress' => $this->percent_progress,
            'completed_lessons' => $this->completed_lessons,
            'words_saved' => $this->words_saved,
            'study_time' => $this->study_time,
            'sequence' => $this->sequence,
        ];
    }
}
