<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\Http\Request;
use RuntimeException;

class MatchController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index()
    {
        return view('admin.matches.index', [
            'matches' => $this->repository->listMatches(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function create()
    {
        return view('admin.matches.create', [
            'tournaments' => $this->repository->listTournaments(),
            'clubs' => $this->repository->listClubs(),
            'schedules' => $this->repository->listSchedules(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->normalizePayload($this->validatePayload($request));

        try {
            $this->repository->createMatch($payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $match = $this->repository->findMatch($id);
        abort_if($match === null, 404);

        return view('admin.matches.edit', [
            'match' => $match,
            'tournaments' => $this->repository->listTournaments(),
            'clubs' => $this->repository->listClubs(),
            'schedules' => $this->repository->listSchedules(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $payload = $this->normalizePayload($this->validatePayload($request));

        try {
            $this->repository->updateMatch($id, $payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deleteMatch($id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.index')->with('success', 'Pertandingan berhasil dihapus.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'tournament_id' => ['required', 'string'],
            'schedule_id' => ['nullable', 'string'],
            'home_club_id' => ['required', 'string', 'different:away_club_id'],
            'away_club_id' => ['required', 'string'],
            'round' => ['required', 'string', 'max:50'],
            'tipoff_at' => ['required', 'date'],
            'venue' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:scheduled,live,selesai,postponed'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:200'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:200'],
            'highlight' => ['nullable', 'string'],
        ]);
    }

    private function normalizePayload(array $payload): array
    {
        return [
            ...$payload,
            'schedule_id' => $payload['schedule_id'] ?? null,
            'home_score' => $payload['home_score'] ?? 0,
            'away_score' => $payload['away_score'] ?? 0,
        ];
    }
}
