<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AppareilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\Foyers\FoyerInvitationController;
use App\Http\Controllers\Foyers\FoyerMemberController;
use App\Http\Middleware\EnsureFoyerMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_foyer}')
    ->middleware(['auth', 'verified', EnsureFoyerMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('appareils', [AppareilController::class, 'layoutAppareil'])->name('layout.appareil');
        Route::get('collocataires', [FoyerMemberController::class, 'layoutMember'])->name('layout.collocataires');
        Route::get('factures', [FactureController::class, 'layoutFacture'])->name('layout.factures');
        Route::get('absences', [AbsenceController::class, 'layoutAbsence'])->name('layout.absences');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [FoyerInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [FoyerInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
