<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    protected $appends = ['study_status'];

    protected $hidden = ['progress'];

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

    public function progress()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'completed_at')
            ->wherePivot('user_id', Auth::id());
    }

    public function getStudyStatusAttribute()
    {
        $pivot = $this->progress->first()?->pivot;

        if (!$pivot) {
            return null;
        }

        return [
            'status' => $pivot->status,
            'completed_at' => $pivot->completed_at,
        ];
    }

    public function scopeByLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }
}
