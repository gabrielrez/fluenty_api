<?php

namespace App\Services;

use App\Models\User;

class SequenceService
{
    public function handle(User $user): void
    {
        $today = today();
        $lastDate = $user->last_sequence_at;

        if ($lastDate && $lastDate->isSameDay($today)) {
            return;
        }

        $user->update([
            'sequence' => $lastDate?->isYesterday() ? $user->sequence + 1 : 1,
            'last_sequence_at' => $today
        ]);
    }

    public function checkAndResetIfNeeded(User $user): void
    {
        $today = today();
        $lastDate = $user->last_sequence_at;

        if (! $lastDate) {
            return;
        }

        if (! $lastDate->isYesterday() && ! $lastDate->isSameDay($today)) {
            $user->update([
                'sequence' => 0,
                'last_sequence_at' => null
            ]);
        }
    }
}
