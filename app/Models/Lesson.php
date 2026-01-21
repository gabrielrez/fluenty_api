<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'text',
        'translation',
        'audio_url',
        'duration',
        'language_id',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'completed_at')
            ->withTimestamps();
    }

    public function scopeByLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }
}
