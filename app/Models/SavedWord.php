<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'word',
        'translation',
        'context',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function scopeBySearch($query, ?string $search)
    {
        return $query->when(
            $search,
            fn($q) => $q->where(function ($q) use ($search) {
                $q->where('word', 'like', "%{$search}%");
            })
        );
    }
}
