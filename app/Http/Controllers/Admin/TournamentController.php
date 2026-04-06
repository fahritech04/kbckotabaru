<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class TournamentController extends Controller
{
    public function __construct(private readonly KbcRepository $repository) {}

    public function index()
    {
        return view('admin.tournaments.index', [
            'tournaments' => $this->repository->listTournaments(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);
        $payload['slug'] = Str::slug($payload['name'].'-'.$payload['season']);

        try {
            $this->repository->createTournament($payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', 'Turnamen berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $tournament = $this->repository->findTournament($id);
        abort_if($tournament === null, 404);

        return view('admin.tournaments.edit', [
            'tournament' => $tournament,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $payload = $this->validatePayload($request);
        $payload['slug'] = Str::slug($payload['name'].'-'.$payload['season']);

        try {
            $this->repository->updateTournament($id, $payload);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', 'Turnamen berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        try {
            $this->repository->deleteTournament($id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.tournaments.index')->with('success', 'Turnamen berhasil dihapus.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'season' => ['required', 'string', 'max:30'],
            'location' => ['required', 'string', 'max:120'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:upcoming,ongoing,finished'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'url'],
        ]);
    }
}
