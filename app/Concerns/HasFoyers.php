<?php

namespace App\Concerns;

use App\Data\FoyerPermissions;
use App\Data\UserFoyer;
use App\Enums\FoyerPermission;
use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

trait HasFoyers
{
    /**
     * Get all of the foyers the user belongs to.
     *
     * @return BelongsToMany<Foyer, $this>
     */
    public function foyers(): BelongsToMany
    {
        return $this->belongsToMany(Foyer::class, 'foyer_members', 'user_id', 'foyer_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get all of the foyers the user owns.
     *
     * @return HasManyThrough<Foyer, Membership, $this>
     */
    public function ownedFoyers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Foyer::class,
            Membership::class,
            'user_id',
            'id',
            'id',
            'foyer_id',
        )->where('foyer_members.role', FoyerRole::Owner->value);
    }

    /**
     * Get all of the memberships for the user.
     *
     * @return HasMany<Membership, $this>
     */
    public function foyerMemberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /**
     * Get the user's current foyer.
     *
     * @return BelongsTo<Foyer, $this>
     */
    public function currentFoyer(): BelongsTo
    {
        return $this->belongsTo(Foyer::class, 'current_foyer_id');
    }

    /**
     * Get the user's personal foyer.
     */
    public function personalFoyer(): ?Foyer
    {
        return $this->ownedFoyers()
            ->where('foyers.is_personal', true)
            ->first();
    }

    /**
     * Switch to the given foyer.
     */
    public function switchFoyer(Foyer $foyer): bool
    {
        if (! $this->belongsToFoyer($foyer)) {
            return false;
        }

        $this->update(['current_foyer_id' => $foyer->id]);
        $this->setRelation('currentFoyer', $foyer);

        URL::defaults(['current_foyer' => $foyer->slug]);

        return true;
    }

    /**
     * Determine if the user belongs to the given foyer.
     */
    public function belongsToFoyer(Foyer $foyer): bool
    {
        return $this->foyers()->where('foyers.id', $foyer->id)->exists();
    }

    /**
     * Determine if the given foyer is the user's current foyer.
     */
    public function isCurrentFoyer(Foyer $foyer): bool
    {
        return $this->current_foyer_id === $foyer->id;
    }

    /**
     * Determine if the user is the owner of the given foyer.
     */
    public function ownsFoyer(Foyer $foyer): bool
    {
        return $this->foyerRole($foyer) === FoyerRole::Owner;
    }

    /**
     * Get the user's role on the given foyer.
     */
    public function foyerRole(Foyer $foyer): ?FoyerRole
    {
        return $this->foyerMemberships()
            ->where('foyer_id', $foyer->id)
            ->first()
            ?->role;
    }

    /**
     * Get the user's foyers as a collection of UserFoyer objects.
     *
     * @return Collection<int, UserFoyer>
     */
    public function toUserFoyers(bool $includeCurrent = false): Collection
    {
        return $this->foyers()
            ->get()
            ->map(fn (Foyer $foyer) => ! $includeCurrent && $this->isCurrentFoyer($foyer) ? null : $this->toUserFoyer($foyer))
            ->filter()
            ->values();
    }

    /**
     * Get the user's foyer as a UserFoyer object.
     */
    public function toUserFoyer(Foyer $foyer): UserFoyer
    {
        $role = $this->foyerRole($foyer);

        return new UserFoyer(
            id: $foyer->id,
            name: $foyer->name,
            slug: $foyer->slug,
            isPersonal: $foyer->is_personal,
            role: $role?->value,
            roleLabel: $role?->label(),
            isCurrent: $this->isCurrentFoyer($foyer),
        );
    }

    /**
     * Get the standard permissions for a foyer as a FoyerPermissions object.
     */
    public function toFoyerPermissions(Foyer $foyer): FoyerPermissions
    {
        $role = $this->foyerRole($foyer);

        return new FoyerPermissions(
            canUpdateFoyer: $role?->hasPermission(FoyerPermission::UpdateFoyer) ?? false,
            canDeleteFoyer: $role?->hasPermission(FoyerPermission::DeleteFoyer) ?? false,
            canAddMember: $role?->hasPermission(FoyerPermission::AddMember) ?? false,
            canUpdateMember: $role?->hasPermission(FoyerPermission::UpdateMember) ?? false,
            canRemoveMember: $role?->hasPermission(FoyerPermission::RemoveMember) ?? false,
            canCreateInvitation: $role?->hasPermission(FoyerPermission::CreateInvitation) ?? false,
            canCancelInvitation: $role?->hasPermission(FoyerPermission::CancelInvitation) ?? false,
        );
    }

    public function fallbackFoyer(?Foyer $excluding = null): ?Foyer
    {
        return $this->foyers()
            ->when($excluding, fn ($query) => $query->where('foyers.id', '!=', $excluding->id))
            ->orderByRaw('LOWER(foyers.name)')
            ->first();
    }

    /**
     * Determine if the user has the given permission on the foyer.
     */
    public function hasFoyerPermission(Foyer $foyer, FoyerPermission $permission): bool
    {
        return $this->foyerRole($foyer)?->hasPermission($permission) ?? false;
    }
}
