<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueFoyerSlugs;
use App\Enums\FoyerRole;
use Database\Factories\FoyerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $is_personal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, FoyerInvitation> $invitations
 * @property-read Collection<int, Membership> $memberships
 * @property-read Collection<int, User> $members
 */
#[Fillable(['name', 'slug', 'is_personal'])]
class Foyer extends Model
{
    /** @use HasFactory<FoyerFactory> */
    use GeneratesUniqueFoyerSlugs, HasFactory, SoftDeletes;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Foyer $foyer) {
            if (empty($foyer->slug)) {
                $foyer->slug = static::generateUniqueFoyerSlug($foyer->name);
            }
        });

        static::updating(function (Foyer $foyer) {
            if ($foyer->isDirty('name')) {
                $foyer->slug = static::generateUniqueFoyerSlug($foyer->name, $foyer->id);
            }
        });
    }

    /**
     * Get the foyer owner.
     */
    public function owner(): ?Model
    {
        return $this->members()
            ->wherePivot('role', FoyerRole::Owner->value)
            ->first();
    }

    /**
     * Get all members of this foyer.
     *
     * @return BelongsToMany<User, $this, Membership, 'pivot'>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'foyer_members', 'foyer_id', 'user_id')
            ->using(Membership::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get all memberships for this foyer.
     *
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get all invitations for this foyer.
     *
     * @return HasMany<FoyerInvitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(FoyerInvitation::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
