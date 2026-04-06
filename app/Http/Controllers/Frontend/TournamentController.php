<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index(): View
    {
        return view('public-site.tournaments.index', [
            'tournaments' => $this->repository->listTournaments(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function show(string $id): View
    {
        $tournament = $this->repository->findTournament($id);

        abort_if($tournament === null, 404);

        $matches = collect($this->repository->listMatches())
            ->filter(fn (array $match): bool => ($match['tournament_id'] ?? null) === $id)
            ->take(12)
            ->values()
            ->all();

        return view('public-site.tournaments.show', [
            'tournament' => $tournament,
            'matches' => $matches,
        ]);
    }
}

