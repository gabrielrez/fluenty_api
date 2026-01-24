<?php

namespace App\Models;

use App\Enums\LessonLevelEnum;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'text',
        'translation',
        'audio_url',
        'duration',
        'category_id',
        'level',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'level' => LessonLevelEnum::class,
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'completed_at')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
