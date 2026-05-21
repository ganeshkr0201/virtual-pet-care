<?php

namespace App\Policies;

use App\Models\Reminder;
use App\Models\User;

class ReminderPolicy
{
    public function view(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id || $user->hasRole('admin');
    }

    public function update(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id || $user->hasRole('admin');
    }
}
