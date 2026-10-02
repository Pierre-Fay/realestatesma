<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    /**
     * Admins may browse all leads; agents may browse their own leads.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->agent !== null;
    }

    /**
     * An agent may update a lead only if it is assigned to their profile.
     */
    public function update(User $user, Lead $lead): bool
    {
        $agent = $user->agent;

        return $agent !== null && $lead->agent_id === $agent->getKey();
    }

    /**
     * Admins may manage any assignment; agents may reassign only their own leads.
     */
    public function assign(User $user, Lead $lead): bool
    {
        return $user->isAdmin() || ($user->isAgent() && $this->update($user, $lead));
    }
}
