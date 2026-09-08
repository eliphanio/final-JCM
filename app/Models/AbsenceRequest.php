<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceRequest extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foyers()
    {
        return $this->belongsTo(Foyer::class);
    }

    protected $fillable = ['start_day', 'end_day', 'user_id', 'foyer_id', 'status'];
    protected $guarded = ['id'];
}
