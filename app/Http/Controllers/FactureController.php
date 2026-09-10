<?php

namespace App\Http\Controllers;

use App\Http\Requests\Factures\FactureRequest;
use App\Http\Requests\Factures\PayementResquest;
use App\Models\Absence;
use App\Models\Facture;
use App\Models\Foyer;
use App\Models\Payement;
use App\Models\Repartition;
use Carbon\Carbon;
use Inertia\Inertia;

class FactureController extends Controller
{
    //
    public function layoutFacture(Foyer $current_foyer)
    {
         $repartitions = Repartition::with(['user', 'facture'])
            ->whereHas('facture', function ($query) use ($current_foyer) {
                $query->where('foyer_id', $current_foyer->id);
            })
            ->get();

        return Inertia::render('facture', [
            "repartitions"=>$repartitions,
            "foyer"=>$current_foyer
        ]);
    }

    public function ajoutFacture(Foyer $current_foyer, FactureRequest $request)
    {
        $data = $request->validated();

        $mois = $data['periode'];
        try {
            Carbon::setLocale('fr');
            $numMois = Carbon::createFromLocaleFormat('F', 'fr', $mois)->month;
        } catch (\Exception $e) {
            return back()->withErrors(['periode' => 'Le mois saisi est invalide.']);
        }

        $periode = Carbon::create(now()->year, $numMois, 1)->format('Y-m-d');

        $exists = Facture::where('foyer_id', $current_foyer->id)
            ->where('periode', $periode)
            ->exists();

        if ($exists) {
            return back()->withErrors(['message' => 'Facture déjà créée pour ce mois']);
        }

        $data['periode'] = $periode;

        $data['foyer_id'] = $current_foyer->id;
        $data['total'] = $data['eau'] + $data['electricite'];
        $data['prix_moyen'] = $data['electricite'] / $data['consomation'];

        // $this->authorize('adminOrModerator', $current_foyer);

        $facture = Facture::create($data);

        // date du mois cpncerner

        $startMonth =

            Carbon::parse($facture->periode)
            ->startOfMonth();

        $endMonth =

            Carbon::parse($facture->periode)
            ->endOfMonth();

        // Nombre de jours dans un mois

        $daysInMonth =
            $startMonth->daysInMonth;

        //    Membre du foyer

        $members = $current_foyer->members;

        $repartitionData = [];

        $totalCoefPresence = 0;

        $totalPartAppareils = 0;

        foreach ($members as $member) {

            //  recuperer absence du membre

            $absences = Absence::where(

                'user_id',
                $member->id
            )

                ->where('foyer_id', $current_foyer->id)
                ->where(function ($query)
                use ($startMonth, $endMonth) {

                    $query

                        ->whereBetween(

                            'start_day',

                            [$startMonth, $endMonth]
                        )

                        ->orWhereBetween(

                            'end_day',

                            [$startMonth, $endMonth]
                        )

                        ->orWhere(function ($q)
                        use ($startMonth, $endMonth) {

                            $q

                                ->where(
                                    'start_day',
                                    '<=',
                                    $startMonth
                                )

                                ->where(
                                    'end_day',
                                    '>=',
                                    $endMonth
                                );
                        });
                })

                ->get();

            //    Jour d'absence

            $absentDays = 0;

            foreach ($absences as $absence) {

                // date d'absence

                $absenceStart =

                    Carbon::parse(
                        $absence->start_day
                    );

                $absenceEnd =

                    Carbon::parse(
                        $absence->end_day
                    );

                //    Intersection avec le mois

                $realStart =

                    $absenceStart
                    ->max($startMonth);

                $realEnd =

                    $absenceEnd
                    ->min($endMonth);

                // Ajout de jour d'absence

                $absentDays +=

                    $realStart
                    ->diffInDays($realEnd)

                    + 1;
            }

            // Jour de presence

            $presentDays =

                $daysInMonth
                -
                $absentDays;



            if ($presentDays < 0) {

                $presentDays = 0;
            }

            //    coef presence

            $coefPresence =

                $presentDays
                /
                $daysInMonth;

            //  total des coefficients

            $totalCoefPresence +=
                $coefPresence;

            // Calcul appareils

            $devices = $member->appareils->where('foyer_id', $current_foyer->id)->get();

            $partAppareil = 0;

            foreach ($devices as $device) {

                //    Consommation appareil

                $consommation = (

                    $device->power_watt

                    *
                    $device->usage

                    *
                    $presentDays

                ) / 1000;

                // prix des appareil

                $prix =

                    $consommation
                    *
                    $facture->prix_moyen;

                //    ajout des prix d'appareil

                $partAppareil += $prix;
            }

            // total part appareils

            $totalPartAppareils +=
                $partAppareil;

            $repartitionData[] = [

                'user_id' => $member->id,

                'coef_presence' => $coefPresence,

                'part_appareil' => $partAppareil
            ];
        }

        //    part commun global

        $partCommuneGlobale =

            $facture->total
            -
            $totalPartAppareils;



        foreach ($repartitionData as $item) {

            // Part commun membre

            $partCommun = (

                $item['coef_presence']
                /
                $totalCoefPresence

            ) * $partCommuneGlobale;

            // Total final

            $total =

                $partCommun
                +
                $item['part_appareil'];



            $repartition = Repartition::create([

                'facture_id' => $facture->id,

                'user_id' => $item['user_id'],

                'coef_presence' =>
                $item['coef_presence'],

                'part_commun' =>
                $partCommun,

                'part_appareil' =>
                $item['part_appareil'],

                'total' =>
                $total
            ]);

            // Mail::to($repartition->user->email)->send(new PartMail($repartition));
        }
    }

    public function payeFacture(Foyer $current_foyer, Repartition $repartition, PayementResquest $request)
    {
       
        $data = $request->validated();

        $data['repartition_id'] = $repartition->id;
        $data['paye_le'] = now();

        // $this->authorize('adminOrModerator', $current_foyer);

        $paid = Payement::create($data);
    }
}
