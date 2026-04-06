<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly ImageUploadService $imageUploadService
    ) {}

    public function index(): View
    {
        return view('frontend.clubs.index', [
            'clubs' => $this->repository->listClubs(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function show(string $id): View
    {
        $club = $this->repository->findClub($id, true);

        abort_if($club === null, 404);

        $matches = collect($this->repository->listMatches())
            ->filter(function (array $match) use ($id): bool {
                return ($match['home_club_id'] ?? null) === $id || ($match['away_club_id'] ?? null) === $id;
            })
            ->take(12)
            ->values()
            ->all();

        return view('frontend.clubs.show', [
            'club' => $club,
            'matches' => $matches,
            'players' => $this->repository->listPlayersByClub($id),
            'clubLogoUrl' => $this->imageUploadService->resolveUrl($club['logo_url'] ?? null),
        ]);
    }
}
