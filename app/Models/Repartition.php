<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repartition extends Model
{
    //
    public function facture(){
        return $this->belongsTo(Facture::class, 'facture_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function payements(){
        return $this->hasMany(Payement::class);
    }

    protected $fillable = ['user_id','facture_id', 'part_commun', 'total', 'part_appareil', 'coef_presence'];
    protected $guarded = ['id'];
}
