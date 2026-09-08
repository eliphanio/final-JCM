<?php

namespace App\Http\Controllers;

use App\Models\Foyer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    //
    public function layoutAbsence(Foyer $foyer){

        return Inertia::render('absence');

    }
}
