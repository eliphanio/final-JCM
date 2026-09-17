<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\AbsenceRequest;
use App\Models\Foyer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AbsenceService
{
    //
    public function DemandeAbsence(array $data, Foyer $foyer)
    {

        $userId = Auth::id();

        $startDay = Carbon::parse($data['start_day'])->startOfDay();
        $endDay = Carbon::parse($data['end_day'])->startOfDay();

        // Vérifier que la date de fin est correcte
        if ($endDay->lt($startDay)) {
            throw ValidationException::withMessages([
                'end_day' => 'La date de retour doit être après la date de départ.'
            ]);
        }

        // Vérifier les chevauchements avec les demandes en attente
        $overlapRequest = AbsenceRequest::where('user_id', $userId)
            ->where('foyer_id', $foyer->id)
            ->whereIn('status', ['En attente'])
            ->where('start_day', '<=', $endDay->toDateString())
            ->where('end_day', '>=', $startDay->toDateString())
            ->exists();

        // Vérifier les chevauchements avec les absences déjà acceptées
        $overlapAbsence = Absence::where('user_id', $userId)
            ->where('foyer_id', $foyer->id)
            ->where('start_day', '<=', $endDay->toDateString())
            ->where('end_day', '>=', $startDay->toDateString())
            ->exists();

        if ($overlapRequest || $overlapAbsence) {
            throw ValidationException::withMessages([
                'start_day' => 'Cette période chevauche une autre absence existante.'
            ]);
        }

        // Préparer les données
        $data['user_id'] = $userId;
        $data['foyer_id'] = $foyer->id;
        $data['status'] = 'En attente';

        // Enregistrer la demande
        AbsenceRequest::create($data);
    }

    public function AcceptAbsence(AbsenceRequest $request)
    {

        $foyer = Foyer::findOrFail($request->foyer_id);
        Gate::authorize('acceptAbsence', $foyer);

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
