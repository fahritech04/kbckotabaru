<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use App\Services\TournamentAutomationService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ClubController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly ImageUploadService $imageUploadService,
        private readonly TournamentAutomationService $tournamentAutomationService
    ) {}

    public function index()
    {
        return view('admin.clubs.index', [
            'clubs' => $this->repository->listClubs(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function show(string $id)
    {
        $club = $this->repository->findClub($id, true);
        abort_if($club === null, 404);

        return view('admin.clubs.show', [
            'club' => $club,
            'players' => $this->repository->listPlayersByClub($id),
            'clubLogoUrl' => $this->imageUploadService->resolveUrl($club['logo_url'] ?? null),
        ]);
    }

    public function create()
    {
        return view('admin.clubs.create', [
            'tournaments' => $this->repository->listTournaments(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);
        $participants = $this->normalizeParticipantRows($request, $payload['players'] ?? []);

        if (! empty($payload['tournament_id']) && $this->repository->findTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak ditemukan.');
        }

        unset($payload['players']);

        $uploadedPaths = [];
        $createdClubId = null;
        $createdPlayerIds = [];

        $payload['logo_url'] = $this->imageUploadService->store($request->file('logo_file'), 'clubs/logos')
            ?? ($payload['logo_url'] ?? null);
        $payload['coach_ktp_url'] = $this->imageUploadService->store($request->file('coach_ktp_file'), 'clubs/coach-ktp')
            ?? ($payload['coach_ktp_url'] ?? null);
        $payload['manager_email'] = strtolower($payload['manager_email'] ?? $payload['club_email']);
        $payload['city'] = $payload['city'] ?? 'Kotabaru';
        $payload['wins'] = (int) ($payload['wins'] ?? 0);
        $payload['losses'] = (int) ($payload['losses'] ?? 0);
        $payload['description'] = $payload['description'] ?? null;

        if (! empty($payload['logo_url'])) {
            $uploadedPaths[] = $payload['logo_url'];
        }
        if (! empty($payload['coach_ktp_url'])) {
            $uploadedPaths[] = $payload['coach_ktp_url'];
        }

        try {
            $club = $this->repository->createClub($payload);
            $createdClubId = $club['id'];

            foreach ($participants as $participant) {
                $photoPath = $this->imageUploadService->store($participant['photo_file'] ?? null, 'clubs/players');
                $ktpPath = $this->imageUploadService->store($participant['ktp_file'] ?? null, 'clubs/players-ktp');

                if ($photoPath !== null) {
                    $uploadedPaths[] = $photoPath;
                }
                if ($ktpPath !== null) {
                    $uploadedPaths[] = $ktpPath;
                }

                $createdPlayer = $this->repository->createPlayer([
                    'club_id' => $club['id'],
                    'name' => $participant['name'],
                    'jersey_number' => $participant['jersey_number'],
                    'photo_url' => $photoPath,
                    'ktp_url' => $ktpPath,
                    'ktp_hash' => $participant['ktp_hash'] ?? null,
                    'position' => null,
                ]);

                $createdPlayerIds[] = $createdPlayer['id'];
            }

            if (! empty($payload['tournament_id'])) {
                $this->tournamentAutomationService->syncTournament($payload['tournament_id']);
            }
        } catch (RuntimeException $exception) {
            foreach ($createdPlayerIds as $playerId) {
                $this->repository->deletePlayer($playerId);
            }

            if ($createdClubId !== null) {
                $this->repository->deleteClub($createdClubId);
            }

            foreach ($uploadedPaths as $path) {
                if (! empty($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Klub berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $club = $this->repository->findClub($id, true);
        abort_if($club === null, 404);

        return view('admin.clubs.edit', [
            'club' => $club,
            'tournaments' => $this->repository->listTournaments(),
            'clubLogoUrl' => $this->imageUploadService->resolveUrl($club['logo_url'] ?? null),
            'players' => $this->repository->listPlayersByClub($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $club = $this->repository->findClub($id, true);
        abort_if($club === null, 404);
        $existingPlayers = $this->repository->listPlayersByClub($id);

        $payload = $this->validatePayload($request, true, $id);
        $participants = $this->normalizeParticipantRows($request, $payload['players'] ?? [], $existingPlayers);

        if (! empty($payload['tournament_id']) && $this->repository->findTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak ditemukan.');
        }

        unset($payload['players']);

        $payload['logo_url'] = $this->imageUploadService->store(
            $request->file('logo_file'),
            'clubs/logos',
            $club['logo_url'] ?? null
        ) ?? ($payload['logo_url'] ?? null);
        $payload['coach_ktp_url'] = $this->imageUploadService->store(
            $request->file('coach_ktp_file'),
            'clubs/coach-ktp',
            $club['coach_ktp_url'] ?? null
        ) ?? ($payload['coach_ktp_url'] ?? null);
        $payload['manager_email'] = strtolower($payload['manager_email'] ?? $payload['club_email']);

        try {
            $this->repository->updateClub($id, $payload);

            $existingPlayersById = collect($existingPlayers)->keyBy('id');

            foreach ($participants as $participant) {
                if (($participant['action'] ?? null) === 'delete') {
                    $playerId = (string) ($participant['id'] ?? '');
                    $player = $existingPlayersById->get($playerId);
                    if ($player === null) {
                        continue;
                    }

                    $this->repository->deletePlayer($playerId);
                    if (! empty($player['photo_url'])) {
                        Storage::disk('public')->delete($player['photo_url']);
                    }
                    if (! empty($player['ktp_url'])) {
                        Storage::disk('public')->delete($player['ktp_url']);
                    }

                    continue;
                }

                if (($participant['action'] ?? null) === 'update') {
                    $playerId = (string) ($participant['id'] ?? '');
                    $player = $existingPlayersById->get($playerId);
                    if ($player === null) {
                        continue;
                    }

                    $photoPath = $this->imageUploadService->store(
                        $participant['photo_file'] ?? null,
                        'clubs/players',
                        $player['photo_url'] ?? null
                    );
                    $ktpPath = $this->imageUploadService->store(
                        $participant['ktp_file'] ?? null,
                        'clubs/players-ktp',
                        $player['ktp_url'] ?? null
                    );

                    $this->repository->updatePlayer($playerId, [
                        'name' => $participant['name'],
                        'jersey_number' => $participant['jersey_number'],
                        'photo_url' => $photoPath,
                        'ktp_url' => $ktpPath,
                        'ktp_hash' => $participant['ktp_hash'] ?? null,
                        'position' => null,
                    ]);

                    continue;
                }

                if (($participant['action'] ?? null) === 'create') {
                    $photoPath = $this->imageUploadService->store($participant['photo_file'] ?? null, 'clubs/players');
                    $ktpPath = $this->imageUploadService->store($participant['ktp_file'] ?? null, 'clubs/players-ktp');

                    $this->repository->createPlayer([
                        'club_id' => $id,
                        'name' => $participant['name'],
                        'jersey_number' => $participant['jersey_number'],
                        'photo_url' => $photoPath,
                        'ktp_url' => $ktpPath,
                        'ktp_hash' => $participant['ktp_hash'] ?? null,
                        'position' => null,
                    ]);
                }
            }

            $previousTournamentId = (string) ($club['tournament_id'] ?? '');
            $currentTournamentId = (string) ($payload['tournament_id'] ?? $previousTournamentId);

            if ($previousTournamentId !== '') {
                $this->tournamentAutomationService->syncTournament($previousTournamentId);
            }
            if ($currentTournamentId !== '' && $currentTournamentId !== $previousTournamentId) {
                $this->tournamentAutomationService->syncTournament($currentTournamentId);
            }
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Klub berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $club = $this->repository->findClub($id, true);
        $players = $this->repository->listPlayersByClub($id);

        try {
            $this->repository->deleteClub($id);

            if ($club !== null && ! empty($club['logo_url'])) {
                Storage::disk('public')->delete($club['logo_url']);
            }
            if ($club !== null && ! empty($club['coach_ktp_url'])) {
                Storage::disk('public')->delete($club['coach_ktp_url']);
            }
            foreach ($players as $player) {
                if (! empty($player['photo_url'])) {
                    Storage::disk('public')->delete($player['photo_url']);
                }
                if (! empty($player['ktp_url'])) {
                    Storage::disk('public')->delete($player['ktp_url']);
                }
            }
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Klub berhasil dihapus.');
    }

    private function validatePayload(Request $request, bool $isEdit = false, ?string $ignoreClubId = null): array
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:120'],
            'coach' => ['required', 'string', 'max:120'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'wins' => ['nullable', 'integer', 'min:0'],
            'losses' => ['nullable', 'integer', 'min:0'],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'logo_file' => [$isEdit ? 'nullable' : 'required', 'image', 'max:4096'],
            'coach_ktp_url' => ['nullable', 'string', 'max:255'],
            'coach_ktp_file' => [$isEdit ? 'nullable' : 'required', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
            'tournament_id' => ['required', 'string'],
            'manager_name' => ['required', 'string', 'max:120'],
            'manager_email' => ['nullable', 'email'],
            'manager_phone' => ['required', 'string', 'max:30'],
            'club_email' => ['required', 'email'],
            'players' => ['nullable', 'array', 'max:15'],
            'players.*.id' => ['nullable', 'string'],
            'players.*.name' => ['nullable', 'string', 'max:150'],
            'players.*.jersey_number' => ['nullable', 'integer', 'min:0', 'max:999'],
            'players.*.photo' => ['nullable', 'image', 'max:4096'],
            'players.*.ktp_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $payload['name'] = trim($payload['name']);
        $payload['club_email'] = strtolower(trim($payload['club_email']));
        $payload['manager_phone'] = trim($payload['manager_phone']);

        if ($this->repository->findClubByName($payload['name'], $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'name' => 'Nama klub sudah terdaftar. Gunakan nama klub lain.',
            ]);
        }

        if ($this->repository->findClubByManagerPhone($payload['manager_phone'], $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'manager_phone' => 'Nomor HP penanggung jawab sudah digunakan klub lain.',
            ]);
        }

        if ($this->repository->findClubByEmail($payload['club_email'], $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'club_email' => 'Email klub sudah digunakan klub lain.',
            ]);
        }

        return $payload;
    }

    private function normalizeParticipantRows(Request $request, array $players, array $existingPlayers = []): array
    {
        $rows = [];
        $jerseyNumbers = [];
        $participantNames = [];
        $ktpHashes = [];
        $existingPlayersById = collect($existingPlayers)->keyBy('id');

        foreach ($players as $index => $player) {
            $playerId = trim((string) ($player['id'] ?? ''));
            $name = trim((string) ($player['name'] ?? ''));
            $jerseyNumber = $player['jersey_number'] ?? null;
            $photoFile = $request->file("players.{$index}.photo");
            $ktpFile = $request->file("players.{$index}.ktp_image");
            $hasJerseyNumber = $jerseyNumber !== null && $jerseyNumber !== '';

            $hasAnyInput = $name !== '' || $hasJerseyNumber || $photoFile !== null || $ktpFile !== null || $playerId !== '';

            if (! $hasAnyInput) {
                continue;
            }

            if ($playerId !== '' && ! $existingPlayersById->has($playerId)) {
                throw ValidationException::withMessages([
                    "players.{$index}.id" => 'Peserta tidak ditemukan.',
                ]);
            }

            if ($playerId !== '' && $name === '' && ! $hasJerseyNumber && $photoFile === null && $ktpFile === null) {
                $rows[] = [
                    'action' => 'delete',
                    'id' => $playerId,
                ];
                continue;
            }

            if ($name === '') {
                throw ValidationException::withMessages([
                    "players.{$index}.name" => 'Nama peserta wajib diisi jika baris peserta digunakan.',
                ]);
            }

            if (! $hasJerseyNumber) {
                throw ValidationException::withMessages([
                    "players.{$index}.jersey_number" => 'Nomor punggung wajib diisi jika baris peserta digunakan.',
                ]);
            }

            $number = (int) $jerseyNumber;
            if (in_array($number, $jerseyNumbers, true)) {
                throw ValidationException::withMessages([
                    "players.{$index}.jersey_number" => 'Nomor punggung duplikat pada form peserta.',
                ]);
            }
            $jerseyNumbers[] = $number;

            $nameKey = mb_strtolower($name);
            if (in_array($nameKey, $participantNames, true)) {
                throw ValidationException::withMessages([
                    "players.{$index}.name" => 'Nama peserta duplikat pada form peserta.',
                ]);
            }
            $participantNames[] = $nameKey;

            $ignorePlayerId = $playerId !== '' ? $playerId : null;

            if ($this->repository->findPlayerByName($name, $ignorePlayerId) !== null) {
                throw ValidationException::withMessages([
                    "players.{$index}.name" => 'Nama peserta sudah terdaftar di database.',
                ]);
            }

            if ($this->repository->findPlayerByJerseyNumber($number, $ignorePlayerId) !== null) {
                throw ValidationException::withMessages([
                    "players.{$index}.jersey_number" => 'Nomor punggung sudah dipakai peserta lain di database.',
                ]);
            }

            $existingPlayer = $playerId !== '' ? $existingPlayersById->get($playerId) : null;
            $ktpHash = $existingPlayer['ktp_hash'] ?? null;
            if ($ktpFile !== null) {
                $ktpHash = $this->hashUploadedFile($ktpFile);
            }

            if ($ktpHash !== null && in_array($ktpHash, $ktpHashes, true)) {
                throw ValidationException::withMessages([
                    "players.{$index}.ktp_image" => 'KTP peserta duplikat pada form peserta.',
                ]);
            }
            if ($ktpHash !== null) {
                $ktpHashes[] = $ktpHash;
            }

            if ($ktpHash !== null && $this->repository->findPlayerByKtpHash($ktpHash, $ignorePlayerId) !== null) {
                throw ValidationException::withMessages([
                    "players.{$index}.ktp_image" => 'KTP peserta sudah terdaftar di database.',
                ]);
            }

            $rows[] = [
                'action' => $playerId !== '' ? 'update' : 'create',
                'id' => $playerId !== '' ? $playerId : null,
                'name' => $name,
                'jersey_number' => $number,
                'photo_file' => $photoFile,
                'ktp_file' => $ktpFile,
                'ktp_hash' => $ktpHash,
            ];
        }

        return $rows;
    }

    private function hashUploadedFile(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        $realPath = $file->getRealPath();

        if ($realPath === false || $realPath === '') {
            return null;
        }

        return hash_file('sha256', $realPath) ?: null;
    }
}
