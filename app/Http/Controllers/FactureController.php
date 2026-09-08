<?php

namespace App\Http\Controllers;

use App\Models\Foyer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactureController extends Controller
{
    //
    public function layoutFacture(Foyer $foyer){

        return Inertia::render('facture');

    }
}
