<?php

namespace App\Policies;

use App\Models\User;

class AgentPolicy
{
    /**
     * Any admin may browse the agent list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Any admin may create an agent account.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
