<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * Admins may browse every property for approval; agents may browse their own listings.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->agent !== null;
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

    /**
     * Only an admin may publish (approve) a listing.
     */
    public function approve(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only an admin may unpublish a listing.
     */
    public function unpublish(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only an admin may delete a listing.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }
}
