<?php

use App\Http\Controllers\Admin\ClubController as AdminClubController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\TournamentController as AdminTournamentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Club\AuthController as ClubAuthController;
use App\Http\Controllers\Club\DashboardController as ClubDashboardController;
use App\Http\Controllers\Club\PlayerController as ClubPlayerController;
use App\Http\Controllers\Frontend\ClubController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InstagramController;
use App\Http\Controllers\Frontend\MatchController;
use App\Http\Controllers\Frontend\ScheduleController;
use App\Http\Controllers\Frontend\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/turnamen', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/turnamen/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/klub', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/klub/{id}', [ClubController::class, 'show'])->name('clubs.show');
Route::get('/jadwal', [ScheduleController::class, 'index'])->name('schedules.index');
Route::get('/pertandingan', [MatchController::class, 'index'])->name('matches.index');
Route::get('/pertandingan/{id}', [MatchController::class, 'show'])->name('matches.show');
Route::get('/instagram', InstagramController::class)->name('instagram.index');
Route::get('/instagram/media/{token}', [InstagramController::class, 'media'])->name('instagram.media');

Route::prefix('daftarklub')
    ->name('club.')
    ->group(function (): void {
        Route::get('/login', [ClubAuthController::class, 'showLogin'])->name('login');
        Route::get('/register', [ClubAuthController::class, 'showRegister'])->name('register');
        Route::get('/auth/google/redirect', [ClubAuthController::class, 'redirectToGoogle'])->name('google.redirect');
        Route::get('/auth/google/callback', [ClubAuthController::class, 'handleGoogleCallback'])->name('google.callback');

        Route::middleware(['auth.session', 'club.only'])->group(function (): void {
            Route::get('/', ClubDashboardController::class)->name('dashboard');
            Route::get('/onboarding', [ClubDashboardController::class, 'showOnboarding'])->name('onboarding');
            Route::post('/onboarding', [ClubDashboardController::class, 'storeOnboarding'])->name('onboarding.store');
            Route::put('/profile', [ClubDashboardController::class, 'updateProfile'])->name('profile.update');
            Route::post('/logout', [ClubAuthController::class, 'logout'])->name('logout');

            Route::get('/players', [ClubPlayerController::class, 'index'])->name('players.index');
            Route::get('/players/create', [ClubPlayerController::class, 'create'])->name('players.create');
            Route::post('/players', [ClubPlayerController::class, 'store'])->name('players.store');
            Route::get('/players/{id}/edit', [ClubPlayerController::class, 'edit'])->name('players.edit');
            Route::put('/players/{id}', [ClubPlayerController::class, 'update'])->name('players.update');
            Route::delete('/players/{id}', [ClubPlayerController::class, 'destroy'])->name('players.destroy');
        });
    });

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'loginAdmin'])->name('login.perform');
        Route::post('/logout', [AuthController::class, 'logoutAdmin'])->name('logout');

        Route::middleware(['auth.session', 'admin.only'])->group(function (): void {
            Route::get('/', AdminDashboardController::class)->name('dashboard');

            Route::get('/turnamen', [AdminTournamentController::class, 'index'])->name('tournaments.index');
            Route::get('/turnamen/create', [AdminTournamentController::class, 'create'])->name('tournaments.create');
            Route::post('/turnamen', [AdminTournamentController::class, 'store'])->name('tournaments.store');
            Route::get('/turnamen/{id}/edit', [AdminTournamentController::class, 'edit'])->name('tournaments.edit');
            Route::put('/turnamen/{id}', [AdminTournamentController::class, 'update'])->name('tournaments.update');
            Route::post('/turnamen/{id}/sync', [AdminTournamentController::class, 'sync'])->name('tournaments.sync');
            Route::get('/turnamen/{id}/draw-group', [AdminTournamentController::class, 'showDrawGroup'])->name('tournaments.draw-group.show');
            Route::post('/turnamen/{id}/draw-group', [AdminTournamentController::class, 'applyDrawGroup'])->name('tournaments.draw-group.apply');
            Route::delete('/turnamen/{id}', [AdminTournamentController::class, 'destroy'])->name('tournaments.destroy');

            Route::get('/klub', [AdminClubController::class, 'index'])->name('clubs.index');
            Route::get('/klub/create', [AdminClubController::class, 'create'])->name('clubs.create');
            Route::post('/klub', [AdminClubController::class, 'store'])->name('clubs.store');
            Route::get('/klub/{id}', [AdminClubController::class, 'show'])->name('clubs.show');
            Route::get('/klub/{id}/edit', [AdminClubController::class, 'edit'])->name('clubs.edit');
            Route::put('/klub/{id}', [AdminClubController::class, 'update'])->name('clubs.update');
            Route::delete('/klub/{id}', [AdminClubController::class, 'destroy'])->name('clubs.destroy');

            Route::get('/jadwal', [AdminScheduleController::class, 'index'])->name('schedules.index');
            Route::get('/jadwal/create', [AdminScheduleController::class, 'create'])->name('schedules.create');
            Route::post('/jadwal', [AdminScheduleController::class, 'store'])->name('schedules.store');
            Route::get('/jadwal/{id}/edit', [AdminScheduleController::class, 'edit'])->name('schedules.edit');
            Route::put('/jadwal/{id}', [AdminScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('/jadwal/{id}', [AdminScheduleController::class, 'destroy'])->name('schedules.destroy');

            Route::get('/pertandingan', [AdminMatchController::class, 'index'])->name('matches.index');
            Route::get('/pertandingan/create', [AdminMatchController::class, 'create'])->name('matches.create');
            Route::post('/pertandingan', [AdminMatchController::class, 'store'])->name('matches.store');
            Route::get('/pertandingan/{id}/edit', [AdminMatchController::class, 'edit'])->name('matches.edit');
            Route::put('/pertandingan/{id}', [AdminMatchController::class, 'update'])->name('matches.update');
            Route::delete('/pertandingan/{id}', [AdminMatchController::class, 'destroy'])->name('matches.destroy');
        });
    });
