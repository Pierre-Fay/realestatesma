<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Any admin may browse the user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Any admin may (re)enable a login.
     */
    public function enable(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    /**
     * An admin may disable a login unless it is their own account or the last
     * remaining active admin (safety rule).
     */
    public function disable(User $user, User $target): bool
    {
        return $user->isAdmin()
            && ! $user->is($target)
            && ! $this->isLastActiveAdmin($target);
    }

    private function isLastActiveAdmin(User $target): bool
    {
        return $target->isAdmin()
            && ! User::query()
                ->where('role', UserRole::ADMIN)
                ->where('is_enabled', true)
                ->whereKeyNot($target->getKey())
                ->exists();
    }
}
