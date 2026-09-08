<?php

use App\Http\Controllers\Foyers\FoyerController;
use App\Http\Controllers\Foyers\FoyerInvitationController;
use App\Http\Controllers\Foyers\FoyerMemberController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Middleware\EnsureFoyerMembership;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/security', [SecurityController::class, 'edit'])
        ->middleware(RequirePassword::class)
        ->name('security.edit');

    Route::put('settings/password', [SecurityController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/appearance')->name('appearance.edit');

    Route::get('settings/foyers', [FoyerController::class, 'index'])->name('foyers.index');
    Route::post('settings/foyers', [FoyerController::class, 'store'])->name('foyers.store');

    Route::middleware(EnsureFoyerMembership::class)->group(function () {
        Route::get('settings/foyers/{foyer}', [FoyerController::class, 'edit'])->name('foyers.edit');
        Route::patch('settings/foyers/{foyer}', [FoyerController::class, 'update'])->name('foyers.update');
        Route::delete('settings/foyers/{foyer}', [FoyerController::class, 'destroy'])->name('foyers.destroy');
        Route::post('settings/foyers/{foyer}/switch', [FoyerController::class, 'switch'])->name('foyers.switch');
        Route::delete('settings/foyers/{foyer}/leave', [FoyerController::class, 'leave'])->name('foyers.leave');

        Route::patch('settings/foyers/{foyer}/members/{user}', [FoyerMemberController::class, 'update'])->name('foyers.members.update');
        Route::delete('settings/foyers/{foyer}/members/{user}', [FoyerMemberController::class, 'destroy'])->name('foyers.members.destroy');

        Route::post('settings/foyers/{foyer}/invitations', [FoyerInvitationController::class, 'store'])->name('foyers.invitations.store');
        Route::delete('settings/foyers/{foyer}/invitations/{invitation}', [FoyerInvitationController::class, 'destroy'])->name('foyers.invitations.destroy');
    });
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
