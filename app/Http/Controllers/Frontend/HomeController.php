<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function __invoke(): View
    {
        $tournaments = $this->repository->listTournaments();
        $clubs = $this->repository->listClubs();
        $upcomingMatches = $this->repository->listUpcomingMatches(6);
        $latestMatches = collect($this->repository->listMatches())->take(6)->all();

        return view('frontend.home', [
            'tournaments' => $tournaments,
            'clubs' => $clubs,
            'upcomingMatches' => $upcomingMatches,
            'latestMatches' => $latestMatches,
            'stats' => $this->repository->dashboardStats(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }
}
