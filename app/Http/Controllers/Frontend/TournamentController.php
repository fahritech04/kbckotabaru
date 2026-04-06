<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use App\Services\TournamentSystemService;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly TournamentSystemService $systemService,
        private readonly ImageUploadService $imageUploadService
    ) {}

    public function index(): View
    {
        $tournaments = collect($this->repository->listTournaments())
            ->map(function (array $tournament): array {
                $tournament['competition_system'] = $this->systemService->normalizeSystemCode($tournament['competition_system'] ?? null);
                $tournament['competition_system_label'] = $this->systemService->systemLabel($tournament['competition_system']);
                $tournament['standings_enabled'] = $this->systemService->supportsStandings($tournament['competition_system']);
                $tournament['hero_image_url'] = $this->imageUploadService->resolveUrl($tournament['hero_image'] ?? null);

                return $tournament;
            })
            ->values()
            ->all();

        return view('public-site.tournaments.index', [
            'tournaments' => $tournaments,
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function show(string $id): View
    {
        $tournament = $this->repository->findTournament($id);

        abort_if($tournament === null, 404);

        $tournament['competition_system'] = $this->systemService->normalizeSystemCode($tournament['competition_system'] ?? null);
        $tournament['competition_system_label'] = $this->systemService->systemLabel($tournament['competition_system']);
        $tournament['competition_system_description'] = $this->systemService->systemDescription($tournament['competition_system']);
        $tournament['hero_image_url'] = $this->imageUploadService->resolveUrl($tournament['hero_image'] ?? null);

        $clubs = $this->repository->listClubsByTournament($id);
        $groupDrawResults = $this->buildGroupDrawResults($tournament, $clubs);
        $matches = collect($this->repository->listMatches())
            ->filter(fn (array $match): bool => ($match['tournament_id'] ?? null) === $id)
            ->values()
            ->all();
        $latestMatches = collect($matches)->take(12)->values()->all();
        $standings = $this->systemService->calculateStandings($tournament, $clubs, $matches);

        return view('public-site.tournaments.show', [
            'tournament' => $tournament,
            'matches' => $latestMatches,
            'allMatches' => $matches,
            'clubs' => $clubs,
            'standings' => $standings,
            'groupDrawResults' => $groupDrawResults,
        ]);
    }

    private function buildGroupDrawResults(array $tournament, array $clubs): array
    {
        $systemCode = $this->systemService->normalizeSystemCode($tournament['competition_system'] ?? null);
        if ($systemCode !== TournamentSystemService::SYSTEM_GROUP_KNOCKOUT) {
            return [];
        }

        $clubsById = collect($clubs)->keyBy('id');
        $raw = (array) ($tournament['group_draw_results'] ?? []);
        $results = [];

        foreach ($raw as $groupName => $clubIds) {
            if (! is_array($clubIds)) {
                continue;
            }

            $members = collect($clubIds)
                ->map(fn ($clubId): ?array => $clubsById->get((string) $clubId))
                ->filter()
                ->map(function (array $club): array {
                    return [
                        'id' => $club['id'],
                        'name' => $club['name'] ?? 'Klub',
                    ];
                })
                ->values()
                ->all();

            $results[] = [
                'group' => (string) $groupName,
                'members' => $members,
            ];
        }

        return $results;
    }
}

