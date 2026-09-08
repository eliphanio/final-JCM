<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appareil extends Model
{
    //
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function foyers(){
        return $this->belongsTo(Foyer::class, 'foyer_id');
    }

    protected $fillable = ['name', 'user_id', 'foyer_id', 'power_watt', 'usage'];
    protected $guarded = ['id'];
}
