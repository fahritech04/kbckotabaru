<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use App\Services\SessionAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PlayerController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly ImageUploadService $imageUploadService
    ) {}

    public function index()
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        return view('club.players.index', [
            'club' => $club,
            'players' => $this->repository->listPlayersByClub($club['id']),
        ]);
    }

    public function create()
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        return view('club.players.create', ['club' => $club]);
    }

    public function store(Request $request)
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        $payload = $this->validatePayload($request);

        $photoPath = $this->imageUploadService->store($request->file('photo'), 'clubs/players');
        $ktpPath = $this->imageUploadService->store($request->file('ktp_image'), 'clubs/players-ktp');

        try {
            $this->repository->createPlayer([
                ...$payload,
                'club_id' => $club['id'],
                'photo_url' => $photoPath,
                'ktp_url' => $ktpPath,
            ]);
        } catch (RuntimeException $exception) {
            if ($photoPath !== null) {
                Storage::disk('public')->delete($photoPath);
            }
            if ($ktpPath !== null) {
                Storage::disk('public')->delete($ktpPath);
            }

            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('club.players.index')->with('success', 'Pemain berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        $player = $this->repository->findPlayer($id);
        abort_if($player === null || ($player['club_id'] ?? null) !== $club['id'], 404);

        return view('club.players.edit', [
            'club' => $club,
            'player' => $player,
            'playerPhotoUrl' => $this->imageUploadService->resolveUrl($player['photo_url'] ?? null),
            'playerKtpUrl' => $this->imageUploadService->resolveUrl($player['ktp_url'] ?? null),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        $player = $this->repository->findPlayer($id);
        abort_if($player === null || ($player['club_id'] ?? null) !== $club['id'], 404);

        $payload = $this->validatePayload($request, $id, $player);

        $payload['photo_url'] = $this->imageUploadService->store(
            $request->file('photo'),
            'clubs/players',
            $player['photo_url'] ?? null
        );
        $payload['ktp_url'] = $this->imageUploadService->store(
            $request->file('ktp_image'),
            'clubs/players-ktp',
            $player['ktp_url'] ?? null
        );

        try {
            $this->repository->updatePlayer($id, $payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('club.players.index')->with('success', 'Data pemain berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $club = $this->resolveClub();
        if ($club === null) {
            return redirect()->route('club.onboarding')->with('error', 'Lengkapi data klub terlebih dahulu.');
        }

        $player = $this->repository->findPlayer($id);
        abort_if($player === null || ($player['club_id'] ?? null) !== $club['id'], 404);

        try {
            $this->repository->deletePlayer($id);

            if (! empty($player['photo_url'])) {
                Storage::disk('public')->delete($player['photo_url']);
            }
            if (! empty($player['ktp_url'])) {
                Storage::disk('public')->delete($player['ktp_url']);
            }
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('club.players.index')->with('success', 'Pemain berhasil dihapus.');
    }

    private function validatePayload(Request $request, ?string $ignorePlayerId = null, ?array $existingPlayer = null): array
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'jersey_number' => ['required', 'integer', 'min:0', 'max:999'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'ktp_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($this->repository->findPlayerByName($payload['name'], $ignorePlayerId) !== null) {
            throw ValidationException::withMessages([
                'name' => 'Nama peserta sudah terdaftar di database.',
            ]);
        }

        if ($this->repository->findPlayerByJerseyNumber((int) $payload['jersey_number'], $ignorePlayerId) !== null) {
            throw ValidationException::withMessages([
                'jersey_number' => 'Nomor punggung sudah digunakan peserta lain di database.',
            ]);
        }

        $ktpHash = $existingPlayer['ktp_hash'] ?? null;
        $ktpFile = $request->file('ktp_image');

        if ($ktpFile !== null) {
            $ktpHash = $this->hashUploadedFile($ktpFile);
        }

        if (
            ! empty($ktpHash)
            && $this->repository->findPlayerByKtpHash($ktpHash, $ignorePlayerId) !== null
        ) {
            throw ValidationException::withMessages([
                'ktp_image' => 'KTP peserta sudah terdaftar di database.',
            ]);
        }

        return [
            'name' => trim($payload['name']),
            'jersey_number' => (int) $payload['jersey_number'],
            'ktp_hash' => $ktpHash,
            'position' => null,
        ];
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
}
