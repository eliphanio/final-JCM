<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{
    //
    public function facture(){
        return $this->belongsTo(Facture::class);
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function repartition(){
        return $this->belongsTo(Repartition::class);
    }

    protected $fillable = ['repartition_id', 'amount', 'paye_le'];
    protected $guarded = ['id'];
}
