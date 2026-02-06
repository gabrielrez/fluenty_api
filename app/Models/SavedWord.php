<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'word',
        'translation',
        'context',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
