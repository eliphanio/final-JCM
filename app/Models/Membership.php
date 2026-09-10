<?php

namespace App\Models;

use App\Enums\FoyerRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $foyer_id
 * @property int $user_id
 * @property FoyerRole $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Foyer $foyer
 * @property-read User $user
 */
#[Fillable(['foyer_id', 'user_id', 'role'])]
class Membership extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'foyer_members';

    public function appareils(){
        return $this->hasMany(Appareil::class);
    }
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Get the foyer that the membership belongs to.
     *
     * @return BelongsTo<Foyer, $this>
     */
    public function foyer(): BelongsTo
    {
        return $this->belongsTo(Foyer::class);
    }

    /**
     * Get the user that belongs to this membership.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => FoyerRole::class,
        ];
    }
}
