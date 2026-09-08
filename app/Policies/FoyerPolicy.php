<?php

namespace App\Policies;

use App\Enums\FoyerPermission;
use App\Models\Foyer;
use App\Models\User;

class FoyerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Foyer $foyer): bool
    {
        return $user->belongsToFoyer($foyer);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::UpdateFoyer);
    }

    /**
     * Determine whether the user can leave the foyer.
     */
    public function leave(User $user, Foyer $foyer): bool
    {
        return ! $foyer->is_personal
            && $user->belongsToFoyer($foyer)
            && ! $user->ownsFoyer($foyer);
    }

    /**
     * Determine whether the user can add a member to the foyer.
     */
    public function addMember(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::AddMember);
    }

    /**
     * Determine whether the user can update a member's role in the foyer.
     */
    public function updateMember(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::UpdateMember);
    }

    /**
     * Determine whether the user can remove a member from the foyer.
     */
    public function removeMember(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::RemoveMember);
    }

    /**
     * Determine whether the user can invite members to the foyer.
     */
    public function inviteMember(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::CreateInvitation);
    }

    /**
     * Determine whether the user can cancel invitations.
     */
    public function cancelInvitation(User $user, Foyer $foyer): bool
    {
        return $user->hasFoyerPermission($foyer, FoyerPermission::CancelInvitation);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Foyer $foyer): bool
    {
        return ! $foyer->is_personal && $user->hasFoyerPermission($foyer, FoyerPermission::DeleteFoyer);
    }
}
