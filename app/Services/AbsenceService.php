<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\AbsenceRequest;
use App\Models\Foyer;
use Illuminate\Support\Facades\Auth;

class AbsenceService
{
    //
    public function DemandeAbsence(array $data, Foyer $foyer){

        $data['user_id'] = Auth::id();

        $data['foyer_id'] = $foyer->id;

        $data['status'] = 'En attente';

        AbsenceRequest::create($data);

    }

    public function AcceptAbsence(AbsenceRequest $request){

        
        // $this->authorize('AcceptAbsence', $foyer);

        $request->update([
            'status' => 'Accepté'
        ]);

        Absence::create([
            'user_id' => $request->user_id,
            'foyer_id' => $request->foyer_id,
            'start_day' => $request->start_day,
            'end_day' => $request->end_day
        ]);

        $request->delete();

        // Pour supprimer tous les demande d'absence qui sont accepté dans la table afin de liberer de l'espace

        // AbsenceRequest::where('status', 'Accepté')->delete();

    }

}