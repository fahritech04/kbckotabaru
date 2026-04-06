<?php

namespace App\Services;

use Carbon\Carbon;

class KbcRepository
{
    public const COLLECTION_USERS = 'users';

    public const COLLECTION_TOURNAMENTS = 'tournaments';

    public const COLLECTION_CLUBS = 'clubs';

    public const COLLECTION_PLAYERS = 'players';

    public const COLLECTION_SCHEDULES = 'schedules';

    public const COLLECTION_MATCHES = 'matches';

    public function __construct(private readonly FirestoreService $firestore) {}

    public function isFirebaseReady(): bool
    {
        return $this->firestore->isAvailable();
    }

    public function firebaseError(): ?string
    {
        return $this->firestore->lastError();
    }

    public function findUserByEmail(string $email): ?array
    {
        return $this->firestore->whereFirst(self::COLLECTION_USERS, 'email', strtolower($email));
    }

    public function findUserByGoogleId(string $googleId): ?array
    {
        return $this->firestore->whereFirst(self::COLLECTION_USERS, 'google_id', $googleId);
    }

    public function findUserById(string $id): ?array
    {
        return $this->firestore->find(self::COLLECTION_USERS, $id);
    }

    public function createUser(array $data): array
    {
        $payload = $this->withTimestamps([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'] ?? null,
            'role' => $data['role'] ?? 'user',
            'club_id' => $data['club_id'] ?? null,
            'google_id' => $data['google_id'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,
            'auth_provider' => $data['auth_provider'] ?? null,
            'email_verified_at' => $data['email_verified_at'] ?? null,
        ], true);

        return $this->firestore->create(self::COLLECTION_USERS, $payload, $data['id'] ?? null);
    }

    public function updateUser(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_USERS, $id, $this->withTimestamps($data));
    }

    public function deleteUser(string $id): bool
    {
        return $this->firestore->delete(self::COLLECTION_USERS, $id);
    }

    public function listTournaments(): array
    {
        return $this->firestore->all(self::COLLECTION_TOURNAMENTS, 'start_date', 'desc');
    }

    public function findTournament(string $id): ?array
    {
        return $this->firestore->find(self::COLLECTION_TOURNAMENTS, $id);
    }

    public function createTournament(array $data): array
    {
        return $this->firestore->create(self::COLLECTION_TOURNAMENTS, $this->withTimestamps($data, true));
    }

    public function updateTournament(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_TOURNAMENTS, $id, $this->withTimestamps($data));
    }

    public function deleteTournament(string $id): bool
    {
        return $this->firestore->delete(self::COLLECTION_TOURNAMENTS, $id);
    }

    public function listClubs(bool $withPlayers = false): array
    {
        $clubs = $this->firestore->all(self::COLLECTION_CLUBS, 'name', 'asc');

        return $this->enrichClubs($clubs, $withPlayers);
    }

    public function findClub(string $id, bool $withPlayers = true): ?array
    {
        $club = $this->firestore->find(self::COLLECTION_CLUBS, $id);

        if ($club === null) {
            return null;
        }

        return $this->enrichClubs([$club], $withPlayers)[0] ?? $club;
    }

    public function findClubByOwnerUserId(string $userId, bool $withPlayers = true): ?array
    {
        $club = $this->firestore->whereFirst(self::COLLECTION_CLUBS, 'owner_user_id', $userId);

        if ($club === null) {
            return null;
        }

        return $this->findClub($club['id'], $withPlayers);
    }

    public function createClub(array $data): array
    {
        return $this->firestore->create(self::COLLECTION_CLUBS, $this->withTimestamps($data, true));
    }

    public function updateClub(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_CLUBS, $id, $this->withTimestamps($data));
    }

    public function deleteClub(string $id): bool
    {
        $players = $this->listPlayersByClub($id);

        foreach ($players as $player) {
            $this->deletePlayer($player['id']);
        }

        return $this->firestore->delete(self::COLLECTION_CLUBS, $id);
    }

    public function listPlayers(): array
    {
        return $this->firestore->all(self::COLLECTION_PLAYERS, 'name', 'asc');
    }

    public function listPlayersByClub(string $clubId): array
    {
        return collect($this->listPlayers())
            ->where('club_id', $clubId)
            ->sortBy(fn (array $player): int => (int) ($player['jersey_number'] ?? 999))
            ->values()
            ->all();
    }

    public function findPlayer(string $id): ?array
    {
        return $this->firestore->find(self::COLLECTION_PLAYERS, $id);
    }

    public function createPlayer(array $data): array
    {
        return $this->firestore->create(self::COLLECTION_PLAYERS, $this->withTimestamps($data, true));
    }

    public function updatePlayer(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_PLAYERS, $id, $this->withTimestamps($data));
    }

    public function deletePlayer(string $id): bool
    {
        return $this->firestore->delete(self::COLLECTION_PLAYERS, $id);
    }

    public function listSchedules(): array
    {
        return $this->enrichSchedules(
            $this->firestore->all(self::COLLECTION_SCHEDULES, 'scheduled_at', 'asc')
        );
    }

    public function findSchedule(string $id): ?array
    {
        $schedule = $this->firestore->find(self::COLLECTION_SCHEDULES, $id);

        if ($schedule === null) {
            return null;
        }

        return $this->enrichSchedules([$schedule])[0];
    }

    public function createSchedule(array $data): array
    {
        return $this->firestore->create(self::COLLECTION_SCHEDULES, $this->withTimestamps($data, true));
    }

    public function updateSchedule(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_SCHEDULES, $id, $this->withTimestamps($data));
    }

    public function deleteSchedule(string $id): bool
    {
        return $this->firestore->delete(self::COLLECTION_SCHEDULES, $id);
    }

    public function listMatches(): array
    {
        return $this->enrichMatches(
            $this->firestore->all(self::COLLECTION_MATCHES, 'tipoff_at', 'desc')
        );
    }

    public function listUpcomingMatches(int $limit = 6): array
    {
        $matches = collect($this->listMatches())
            ->filter(function (array $match): bool {
                $tipoff = $this->parseDate($match['tipoff_at'] ?? null);

                return $tipoff?->greaterThanOrEqualTo(now()) ?? false;
            })
            ->sortBy('tipoff_at')
            ->take($limit)
            ->values()
            ->all();

        return $matches;
    }

    public function findMatch(string $id): ?array
    {
        $match = $this->firestore->find(self::COLLECTION_MATCHES, $id);

        if ($match === null) {
            return null;
        }

        return $this->enrichMatches([$match])[0];
    }

    public function createMatch(array $data): array
    {
        return $this->firestore->create(self::COLLECTION_MATCHES, $this->withTimestamps($data, true));
    }

    public function updateMatch(string $id, array $data): ?array
    {
        return $this->firestore->update(self::COLLECTION_MATCHES, $id, $this->withTimestamps($data));
    }

    public function deleteMatch(string $id): bool
    {
        return $this->firestore->delete(self::COLLECTION_MATCHES, $id);
    }

    public function dashboardStats(): array
    {
        $matches = $this->listMatches();
        $finishedMatches = collect($matches)->filter(fn (array $match): bool => ($match['status'] ?? null) === 'selesai')->count();

        return [
            'tournaments' => count($this->listTournaments()),
            'clubs' => count($this->listClubs()),
            'schedules' => count($this->listSchedules()),
            'matches' => count($matches),
            'finished_matches' => $finishedMatches,
        ];
    }

    private function enrichMatches(array $matches): array
    {
        $clubs = collect($this->listClubs())->keyBy('id');
        $tournaments = collect($this->listTournaments())->keyBy('id');
        $schedules = collect($this->listSchedules())->keyBy('id');

        return collect($matches)
            ->map(function (array $match) use ($clubs, $tournaments, $schedules): array {
                return [
                    ...$match,
                    'home_club' => $clubs->get($match['home_club_id'] ?? ''),
                    'away_club' => $clubs->get($match['away_club_id'] ?? ''),
                    'tournament' => $tournaments->get($match['tournament_id'] ?? ''),
                    'schedule' => $schedules->get($match['schedule_id'] ?? ''),
                ];
            })
            ->all();
    }

    private function enrichSchedules(array $schedules): array
    {
        $tournaments = collect($this->listTournaments())->keyBy('id');

        return collect($schedules)->map(function (array $schedule) use ($tournaments): array {
            return [
                ...$schedule,
                'tournament' => $tournaments->get($schedule['tournament_id'] ?? ''),
            ];
        })->all();
    }

    private function enrichClubs(array $clubs, bool $withPlayers): array
    {
        $tournaments = collect($this->listTournaments())->keyBy('id');
        $playersGrouped = collect($this->listPlayers())->groupBy('club_id');

        return collect($clubs)
            ->map(function (array $club) use ($tournaments, $playersGrouped, $withPlayers): array {
                $players = $playersGrouped->get($club['id'] ?? '', collect())->values()->all();

                $payload = [
                    ...$club,
                    'tournament' => $tournaments->get($club['tournament_id'] ?? ''),
                    'players_count' => count($players),
                ];

                if ($withPlayers) {
                    $payload['players'] = $players;
                }

                return $payload;
            })
            ->values()
            ->all();
    }

    private function withTimestamps(array $data, bool $creating = false): array
    {
        $now = now()->toIso8601String();
        $payload = collect($data)
            ->reject(fn (mixed $value): bool => $value === null)
            ->all();

        if ($creating && ! array_key_exists('created_at', $payload)) {
            $payload['created_at'] = $now;
        }

        $payload['updated_at'] = $now;

        return $payload;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
