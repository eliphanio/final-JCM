<?php

namespace App\Http\Controllers;

use App\Models\Foyer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppareilController extends Controller
{
    //
    public function layoutAppareil(Foyer $foyer){

        return Inertia::render('appareil');

    }
}
