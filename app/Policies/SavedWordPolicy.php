<?php

namespace App\Policies;

use App\Models\SavedWord;
use App\Models\User;

class SavedWordPolicy
{
    public function delete(User $user, SavedWord $savedWord): bool
    {
        return $savedWord->user_id === $user->id;
    }
}
