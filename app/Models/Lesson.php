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
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
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
