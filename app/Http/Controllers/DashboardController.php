<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\Facture;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Models\Membership;
use App\Models\Repartition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Foyer $current_foyer): Response
    {
        $email = strtolower($request->user()->email);
        $facture = Facture::with('repartition')->where('foyer_id', $current_foyer->id)->latest()->first();


        $pendingInvitations = FoyerInvitation::query()
            ->with(['inviter', 'foyer'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn(FoyerInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'foyer' => [
                    'name' => $invitation->foyer->name,
                    'slug' => $invitation->foyer->slug,
                ],
            ]);

        $factureId = $facture?->id;

        $consomationDevice = $factureId
            ? Repartition::where('facture_id', $factureId)->sum('part_appareil')
            : 0;

        $consomationUser = $factureId
            ? Repartition::where('facture_id', $factureId)->sum('part_commun')
            : 0;


        $echeance = $facture?->due_date;

        $joursRestant = $echeance
            ? now()->diffInDays($echeance, false)
            : null;
        $joursRestant = floor($joursRestant);

        // Récupérer uniquement les 6 derniers mois

        $factures = Facture::where('foyer_id', $current_foyer->id)
            ->where('periode', '>=', now()->subMonths(11)->startOfMonth())
            ->orderBy('periode')
            ->get();

        //  Grouper par mois

        $grouped = $factures->groupBy(function ($facture) {
            return Carbon::parse($facture->periode)->format('Y-m');
        });

        $labels = [];
        $eauData = [];
        $electriciteData = [];

        // Forcer exactement 6 mois (même si vide)

        $derniereFacture = Facture::where('foyer_id', $current_foyer->id)
            ->orderByDesc('periode')
            ->first();

        if ($derniereFacture) {
            $fin = \Carbon\Carbon::parse($derniereFacture->periode);
            for ($i = 5; $i >= 0; $i--) {

                $date = $fin->copy()->subMonths($i);
                $key = $date->format('Y-m');

                $labels[] = $date->locale('fr')->translatedFormat('F Y');

                $eauData[] = isset($grouped[$key])
                    ? $grouped[$key]->sum('eau')
                    : 0;

                $electriciteData[] = isset($grouped[$key])
                    ? $grouped[$key]->sum('electricite')
                    : 0;
            }
        }


        return Inertia::render('dashboard', [
            'pendingInvitations' => $pendingInvitations,
            'absenceRequests' => AbsenceRequest::with('user')->where('foyer_id', $current_foyer->id)->get(),
            'members' => $current_foyer->members()->get(),
            'appareils' => $current_foyer->appareils()->get(),
            'facture' => $facture,
            'labels'=>$labels,
            'eau'=>$eauData,
            'electricite'=>$electriciteData,
            'jourRestant'=>$joursRestant
        ]);
    }
}
