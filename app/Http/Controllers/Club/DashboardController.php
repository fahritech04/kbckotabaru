<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use App\Services\SessionAuthService;
use App\Services\TournamentAutomationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DashboardController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly ImageUploadService $imageUploadService,
        private readonly TournamentAutomationService $tournamentAutomationService
    ) {}

    public function __invoke()
    {
        $club = $this->resolveClub();

        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        return view('club.dashboard', [
            'club' => $club,
            'players' => $this->repository->listPlayersByClub($club['id']),
            'tournaments' => $this->listSelectableTournaments(),
            'clubLogoUrl' => $this->imageUploadService->resolveUrl($club['logo_url'] ?? null),
            'coachKtpUrl' => $this->imageUploadService->resolveUrl($club['coach_ktp_url'] ?? null),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function showOnboarding()
    {
        if ($this->resolveClub() !== null) {
            return redirect()->route('club.dashboard');
        }

        $authUser = session(SessionAuthService::SESSION_KEY_CLUB);
        abort_if($authUser === null, 403);

        return view('club.onboarding', [
            'tournaments' => $this->listSelectableTournaments(),
            'authUser' => $authUser,
        ]);
    }

    public function storeOnboarding(Request $request): RedirectResponse
    {
        if ($this->resolveClub() !== null) {
            return redirect()->route('club.dashboard');
        }

        $authUser = session(SessionAuthService::SESSION_KEY_CLUB);
        abort_if($authUser === null, 403);

        $payload = $request->validate([
            'manager_name' => ['required', 'string', 'max:120'],
            'manager_phone' => ['required', 'string', 'max:30'],
            'club_email' => ['required', 'email'],
            'club_name' => ['required', 'string', 'max:150'],
            'coach' => ['required', 'string', 'max:120'],
            'tournament_id' => ['required', 'string'],
            'club_logo' => ['required', 'image', 'max:4096'],
            'coach_ktp' => ['required', 'image', 'max:4096'],
            'players' => ['nullable', 'array', 'max:15'],
            'players.*.name' => ['nullable', 'string', 'max:150'],
            'players.*.jersey_number' => ['nullable', 'integer', 'min:0', 'max:999'],
            'players.*.photo' => ['nullable', 'image', 'max:4096'],
            'players.*.ktp_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($this->findSelectableTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak tersedia untuk pendaftaran. Pilih turnamen dengan status upcoming atau ongoing.');
        }

        $this->assertClubUniqueness(
            $payload['club_name'],
            $payload['manager_phone'],
            $payload['club_email']
        );

        $participants = $this->normalizeParticipantRows($request, $payload['players'] ?? []);
        $this->assertParticipantUniqueness($participants);

        $uploadedPaths = [];
        $createdPlayerIds = [];
        $createdClubId = null;

        $clubLogoPath = $this->imageUploadService->store($request->file('club_logo'), 'clubs/logos');
        $coachKtpPath = $this->imageUploadService->store($request->file('coach_ktp'), 'clubs/coach-ktp');

        if ($clubLogoPath !== null) {
            $uploadedPaths[] = $clubLogoPath;
        }

        if ($coachKtpPath !== null) {
            $uploadedPaths[] = $coachKtpPath;
        }

        try {
            $club = $this->repository->createClub([
                'name' => $payload['club_name'],
                'city' => 'Kotabaru',
                'coach' => $payload['coach'],
                'wins' => 0,
                'losses' => 0,
                'description' => null,
                'logo_url' => $clubLogoPath,
                'coach_ktp_url' => $coachKtpPath,
                'tournament_id' => $payload['tournament_id'],
                'manager_name' => $payload['manager_name'],
                'manager_email' => strtolower($authUser['email'] ?? $payload['club_email']),
                'manager_phone' => $payload['manager_phone'],
                'club_email' => strtolower($payload['club_email']),
                'owner_user_id' => $authUser['id'],
            ]);

            $createdClubId = $club['id'];

            $this->repository->updateUser($authUser['id'], [
                'name' => $payload['manager_name'],
                'club_id' => $club['id'],
            ]);
            session()->put(SessionAuthService::SESSION_KEY_CLUB.'.name', $payload['manager_name']);
            session()->put(SessionAuthService::SESSION_KEY_CLUB.'.club_id', $club['id']);

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

            $this->tournamentAutomationService->syncTournament($payload['tournament_id']);
        } catch (RuntimeException $exception) {
            foreach ($createdPlayerIds as $playerId) {
                $this->repository->deletePlayer($playerId);
            }

            if ($createdClubId !== null) {
                $this->repository->deleteClub($createdClubId);
                $this->repository->updateUser($authUser['id'], ['club_id' => null]);
                session()->put(SessionAuthService::SESSION_KEY_CLUB.'.club_id', null);
            }

            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('club.dashboard')->with('success', 'Data klub berhasil disimpan.');
    }

    public function updateProfile(Request $request)
    {
        $club = $this->resolveClub();

        abort_if($club === null, 404, 'Data klub tidak ditemukan.');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'coach' => ['required', 'string', 'max:120'],
            'tournament_id' => ['required', 'string'],
            'manager_name' => ['required', 'string', 'max:120'],
            'manager_phone' => ['required', 'string', 'max:30'],
            'club_email' => ['required', 'email'],
            'description' => ['nullable', 'string'],
            'club_logo' => ['nullable', 'image', 'max:4096'],
            'coach_ktp' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($this->findSelectableTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak tersedia untuk pendaftaran. Pilih turnamen dengan status upcoming atau ongoing.');
        }

        $this->assertClubUniqueness(
            $payload['name'],
            $payload['manager_phone'],
            $payload['club_email'],
            $club['id']
        );

        $payload['logo_url'] = $this->imageUploadService->store(
            $request->file('club_logo'),
            'clubs/logos',
            $club['logo_url'] ?? null
        );
        $payload['coach_ktp_url'] = $this->imageUploadService->store(
            $request->file('coach_ktp'),
            'clubs/coach-ktp',
            $club['coach_ktp_url'] ?? null
        );

        try {
            $previousTournamentId = (string) ($club['tournament_id'] ?? '');

            $this->repository->updateClub($club['id'], [
                'name' => $payload['name'],
                'coach' => $payload['coach'],
                'tournament_id' => $payload['tournament_id'],
                'manager_name' => $payload['manager_name'],
                'manager_phone' => $payload['manager_phone'],
                'club_email' => strtolower($payload['club_email']),
                'description' => $payload['description'] ?? null,
                'logo_url' => $payload['logo_url'] ?? ($club['logo_url'] ?? null),
                'coach_ktp_url' => $payload['coach_ktp_url'] ?? ($club['coach_ktp_url'] ?? null),
            ]);

            $this->tournamentAutomationService->syncTournament($payload['tournament_id']);
            if ($previousTournamentId !== '' && $previousTournamentId !== $payload['tournament_id']) {
                $this->tournamentAutomationService->syncTournament($previousTournamentId);
            }

            $ownerId = $club['owner_user_id'] ?? null;
            if ($ownerId !== null) {
                $this->repository->updateUser($ownerId, ['name' => $payload['manager_name']]);
                session()->put(SessionAuthService::SESSION_KEY_CLUB.'.name', $payload['manager_name']);
            }
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('club.dashboard')->with('success', 'Profil klub berhasil diperbarui.');
    }

    private function normalizeParticipantRows(Request $request, array $players): array
    {
        $rows = [];
        $jerseyNumbers = [];
        $participantNames = [];
        $ktpHashes = [];

        foreach ($players as $index => $player) {
            $name = trim((string) ($player['name'] ?? ''));
            $jerseyNumber = $player['jersey_number'] ?? null;
            $photoFile = $request->file("players.{$index}.photo");
            $ktpFile = $request->file("players.{$index}.ktp_image");
            $hasJerseyNumber = $jerseyNumber !== null && $jerseyNumber !== '';

            $hasAnyInput = $name !== '' || $hasJerseyNumber || $photoFile !== null || $ktpFile !== null;

            if (! $hasAnyInput) {
                continue;
            }

            if ($name === '') {
                throw ValidationException::withMessages([
                    "players.{$index}.name" => 'Nama peserta wajib diisi jika baris peserta digunakan.',
                ]);
            }

            if ($jerseyNumber === null || $jerseyNumber === '') {
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

            $ktpHash = $this->hashUploadedFile($ktpFile);
            if ($ktpHash !== null && in_array($ktpHash, $ktpHashes, true)) {
                throw ValidationException::withMessages([
                    "players.{$index}.ktp_image" => 'KTP peserta duplikat pada form peserta.',
                ]);
            }
            if ($ktpHash !== null) {
                $ktpHashes[] = $ktpHash;
            }

            $rows[] = [
                'name' => $name,
                'jersey_number' => $number,
                'photo_file' => $photoFile,
                'ktp_file' => $ktpFile,
                'ktp_hash' => $ktpHash,
                'source_index' => $index,
            ];
        }

        return $rows;
    }

    private function assertClubUniqueness(
        string $clubName,
        string $managerPhone,
        string $clubEmail,
        ?string $ignoreClubId = null
    ): void {
        if ($this->repository->findClubByName($clubName, $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'club_name' => 'Nama klub sudah terdaftar. Gunakan nama klub lain.',
                'name' => 'Nama klub sudah terdaftar. Gunakan nama klub lain.',
            ]);
        }

        if ($this->repository->findClubByManagerPhone($managerPhone, $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'manager_phone' => 'Nomor HP penanggung jawab sudah digunakan klub lain.',
            ]);
        }

        if ($this->repository->findClubByEmail($clubEmail, $ignoreClubId) !== null) {
            throw ValidationException::withMessages([
                'club_email' => 'Email klub sudah digunakan klub lain.',
            ]);
        }
    }

    private function assertParticipantUniqueness(array $participants): void
    {
        foreach ($participants as $participant) {
            $sourceIndex = (int) ($participant['source_index'] ?? 0);

            if ($this->repository->findPlayerByName($participant['name']) !== null) {
                throw ValidationException::withMessages([
                    "players.{$sourceIndex}.name" => 'Nama peserta sudah terdaftar di database.',
                ]);
            }

            if ($this->repository->findPlayerByJerseyNumber((int) $participant['jersey_number']) !== null) {
                throw ValidationException::withMessages([
                    "players.{$sourceIndex}.jersey_number" => 'Nomor punggung sudah dipakai peserta lain di database.',
                ]);
            }

            if (
                ! empty($participant['ktp_hash'])
                && $this->repository->findPlayerByKtpHash($participant['ktp_hash']) !== null
            ) {
                throw ValidationException::withMessages([
                    "players.{$sourceIndex}.ktp_image" => 'KTP peserta sudah terdaftar di database.',
                ]);
            }
        }
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

    private function resolveClub(): ?array
    {
        $authUser = session(SessionAuthService::SESSION_KEY_CLUB);

        if ($authUser === null) {
            return null;
        }

        $clubId = $authUser['club_id'] ?? null;

        if ($clubId !== null) {
            return $this->repository->findClub($clubId, true);
        }

        $club = $this->repository->findClubByOwnerUserId($authUser['id'], true);

        if ($club !== null) {
            session()->put(SessionAuthService::SESSION_KEY_CLUB.'.club_id', $club['id']);
        }

        return $club;
    }

    private function listSelectableTournaments(): array
    {
        return collect($this->repository->listTournaments())
            ->filter(function (array $tournament): bool {
                $status = strtolower(trim((string) ($tournament['status'] ?? '')));

                return in_array($status, ['upcoming', 'ongoing'], true);
            })
            ->values()
            ->all();
    }

    private function findSelectableTournament(string $tournamentId): ?array
    {
        return collect($this->listSelectableTournaments())
            ->firstWhere('id', $tournamentId);
    }
}
