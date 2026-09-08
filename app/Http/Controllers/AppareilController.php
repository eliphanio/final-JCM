<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appareils\AjoutAppareilResquest;
use App\Models\Appareil;
use App\Models\Foyer;
use App\Services\AjoutAppareilService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppareilController extends Controller
{
    //
    public function layoutAppareil(Foyer $current_foyer)
    {

        $devices = Appareil::with('user')->where('foyer_id', $current_foyer->id)->get();

        return Inertia::render('appareil', [
            'devices' => $devices,
            'foyer' => $current_foyer,
        ]);
    }

    public function AjoutAppareil(AjoutAppareilResquest $request, Foyer $current_foyer, AjoutAppareilService $service)
    {
        $device = $request->validated();

        $service->AjoutAppareil($device, $current_foyer);

        return redirect()->route('layout.appareil', ['foyer' => $current_foyer->id])->with('success', 'Appareil ajouté avec succès.');
    }

    public function destroy(Foyer $current_foyer, Appareil $appareil)
    {
        // Vérifier si l'appareil appartient au foyer actuel
        if ($appareil->foyer_id !== $current_foyer->id) {
            abort(403, 'Unauthorized action.');
        }

        // Supprimer l'appareil
        $appareil->delete();

        return redirect()->route('layout.appareil', ['foyer' => $current_foyer->id])->with('success', 'Appareil supprimé avec succès.');
    }
}
