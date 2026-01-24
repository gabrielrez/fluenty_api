<?php

namespace App\Models;

use App\Enums\LessonUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sequence',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sequence' => 'integer',
        ];
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class)
            ->withPivot('status', 'completed_at')
            ->withTimestamps();
    }

    public function completedLessons()
    {
        return $this->lessons()->wherePivot('status', LessonUserStatus::Completed->value);
    }

    public function inProgressLessons()
    {
        return $this->lessons()->wherePivot('status', LessonUserStatus::InProgress->value);
    }
}
