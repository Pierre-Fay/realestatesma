<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    /**
     * An agent may browse their own leads.
     */
    public function viewAny(User $user): bool
    {
        return $user->agent !== null;
    }

    /**
     * An agent may update a lead only if it is assigned to their profile.
     */
    public function update(User $user, Lead $lead): bool
    {
        $agent = $user->agent;

        return $agent !== null && $lead->agent_id === $agent->getKey();
    }
}
