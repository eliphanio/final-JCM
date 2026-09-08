<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    //
     public function absences(){
        return $this->hasMany(Absence::class);
    }

    public function repartition(){
        return $this->hasMany(Repartition::class);
    }

    public function payement(){
        return $this->hasMany(Payement::class);
    }

    public function foyer(){
        return $this->belongsTo(Foyer::class);
    }

    protected $fillable = ['foyer_id', 'eau', 'electricite', 'prix_moyen', 'total', 'status', 'periode', 'due_date', 'consomation'];
    protected $guarded = ['id'];
}
