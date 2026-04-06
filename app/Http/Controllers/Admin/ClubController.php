<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\KbcRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ClubController extends Controller
{
    public function __construct(
        private readonly KbcRepository $repository,
        private readonly ImageUploadService $imageUploadService
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

        if (! empty($payload['tournament_id']) && $this->repository->findTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak ditemukan.');
        }

        $payload['logo_url'] = $this->imageUploadService->store($request->file('logo_file'), 'clubs/logos')
            ?? ($payload['logo_url'] ?? null);
        $payload['coach_ktp_url'] = $this->imageUploadService->store($request->file('coach_ktp_file'), 'clubs/coach-ktp')
            ?? ($payload['coach_ktp_url'] ?? null);

        try {
            $this->repository->createClub($payload);
        } catch (RuntimeException $exception) {
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
        ]);
    }

    public function update(Request $request, string $id)
    {
        $club = $this->repository->findClub($id, true);
        abort_if($club === null, 404);

        $payload = $this->validatePayload($request);

        if (! empty($payload['tournament_id']) && $this->repository->findTournament($payload['tournament_id']) === null) {
            return back()->withInput()->with('error', 'Turnamen yang dipilih tidak ditemukan.');
        }

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

        try {
            $this->repository->updateClub($id, $payload);
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

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:120'],
            'coach' => ['required', 'string', 'max:120'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'wins' => ['nullable', 'integer', 'min:0'],
            'losses' => ['nullable', 'integer', 'min:0'],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'max:4096'],
            'coach_ktp_url' => ['nullable', 'string', 'max:255'],
            'coach_ktp_file' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
            'tournament_id' => ['nullable', 'string'],
            'manager_name' => ['nullable', 'string', 'max:120'],
            'manager_email' => ['nullable', 'email'],
            'manager_phone' => ['nullable', 'string', 'max:30'],
            'club_email' => ['nullable', 'email'],
        ]);
    }
}
