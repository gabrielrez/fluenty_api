<?php

namespace App\Models;

use App\Enums\LessonLevelEnum;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'description',
        'text',
        'translation',
        'image_url',
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

    public function scopeByLevel($query, $level)
    {
        return $query->when(
            $level,
            fn($q) => $q->where('level', $level)
        );
    }

    public function scopeByCategory($query, $category_id)
    {
        return $query->when(
            $category_id,
            fn($q) => $q->where('category_id', $category_id)
        );
    }

    public function scopeOnlyStarted($query, bool $only_started, ?int $user_id)
    {
        return $query->when(
            $only_started && $user_id,
            fn($q) => $q->whereHas(
                'users',
                fn($q) => $q->where('user_id', $user_id)
            )
        );
    }

    public function scopeByStudyStatus($query, ?string $status, ?int $user_id)
    {
        return $query->when(
            $status && $user_id,
            fn($q) => $q->whereHas(
                'users',
                fn($q) => $q
                    ->where('user_id', $user_id)
                    ->wherePivot('status', $status)
            )
        );
    }

    public function scopeBySearch($query, ?string $search)
    {
        return $query->when(
            $search,
            fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('text', 'description', 'like', "%{$search}%")
        );
    }
}
