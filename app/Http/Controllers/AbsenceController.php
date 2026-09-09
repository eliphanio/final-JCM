<?php

namespace App\Http\Controllers;

use App\Http\Requests\Absences\AbsenceSendResquest;
use App\Models\Absence;
use App\Models\AbsenceRequest;
use App\Models\Foyer;
use App\Services\AbsenceService;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    //
    public function layoutAbsence(Foyer $current_foyer)
    {

        $absenceRequests = AbsenceRequest::with('user')->where('foyer_id', $current_foyer->id)->get();
        $absences = Absence::with('user')->where('foyer_id', $current_foyer->id)->get();

        // dd($absences);

        return Inertia::render('absence', [
            'foyer' => $current_foyer,
            'absenceRequests' => $absenceRequests,
            'absences' => $absences,
        ]);
    }

    public function RequestAbsence(AbsenceSendResquest $request, Foyer $current_foyer, AbsenceService $service)
    {
        $validatedData = $request->validated();

        $service->DemandeAbsence($validatedData, $current_foyer);

        return back()->with('succes', 'Démande envoyé');
    }

    public function AcceptAbsence($current_foyer, AbsenceRequest $absenceRequest, AbsenceService $service)
    {
        // dd($absenceRequest);

        $service->AcceptAbsence($absenceRequest);

    }
}
