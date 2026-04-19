<?php

namespace App\Models;

use App\Enums\LessonLevelEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

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
        'source',
        'is_free'
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'level' => LessonLevelEnum::class,
            'is_free' => 'boolean'
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

    public function savedWords()
    {
        return $this->hasMany(SavedWord::class);
    }

    public function text(): Attribute
    {
        return Attribute::make(
            get: fn($text) => str_replace('\\n', "\n", $text)
        );
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

    public function scopeOnlyStarted($query, bool $only_started, ?User $user)
    {
        return $query->when(
            $only_started && $user,
            fn($q) => $q->whereHas(
                'users',
                fn($q) => $q->where('user_id', $user->id)
            )
        );
    }

    public function scopeNotStarted($query, bool $not_started, ?User $user)
    {
        return $query->when(
            $not_started && $user,
            fn($q) => $q->whereDoesntHave('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
        );
    }

    public function scopeByStudyStatus($query, ?string $status, ?User $user)
    {
        return $query->when(
            $status && $user,
            fn($q) => $q->whereHas('users', function ($q) use ($user, $status) {
                $q->where('users.id', $user->id)
                    ->where('lesson_user.status', $status);
            })
        );
    }

    public function scopeBySearch($query, ?string $search)
    {
        return $query->when(
            $search,
            fn($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('text', 'like', "%{$search}%");
            })
        );
    }

    public function scopeOrderByUserLatestProgress($query, bool $latest, ?User $user)
    {
        return $query->when(
            $latest && $user,
            fn($q) => $q
                ->join('lesson_user', function ($join) use ($user) {
                    $join->on('lessons.id', '=', 'lesson_user.lesson_id')
                        ->where('lesson_user.user_id', $user->id);
                })
                ->orderByDesc('lesson_user.created_at')
                ->select('lessons.*')
        );
    }

    public function scopeIsFree($query, ?bool $is_free)
    {
        return $query->when(
            !is_null($is_free),
            fn($q) => $q->where('is_free', $is_free)
        );
    }
}
