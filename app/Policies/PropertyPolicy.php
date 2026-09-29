<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * An agent may browse their own listings.
     */
    public function viewAny(User $user): bool
    {
        return $user->agent !== null;
    }

    /**
     * An agent may create a listing.
     */
    public function create(User $user): bool
    {
        return $user->agent !== null;
    }

    /**
     * An agent may update a listing only if it is attached to their profile.
     */
    public function update(User $user, Property $property): bool
    {
        $agent = $user->agent;

        return $agent !== null
            && $property->agents()->whereKey($agent->getKey())->exists();
    }
}
